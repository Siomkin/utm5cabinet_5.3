<?php
class Urfa_Packet
{
    private $data = array();
    private $pos;
    private $code;

    function setCode($code)
    {
        $this->code = $code;
        return TRUE;
    }

    function getCode()
    {
        return $this->code;
    }

    function size()
    {
        return count($this->data);
    }

    function getPos()
    {
        return $this->pos;
    }

    function clear()
    {
        $this->data = array();
        $this->code = 0;
        $this->pos = 0;
        return TRUE;
    }

    function recvPacket($sock)
    {
        $this->data = array();
        $header = $sock->read(4);
        if (strlen($header) != 4) {
            return FALSE;
        }
        $fields = unpack("Ccode/Cver/nsize", $header);
        if ($fields['ver'] != 35) {
            return FALSE;
        }
        $packet = $sock->read($fields['size'] - 4);
        if (strlen($packet) < 4) {
            return FALSE;
        }
        $pos = 0;
        $count = 0;
        while ($pos <= strlen($packet) - 4) {
            $attr_data = substr($packet, $pos, 4);
            $attr = unpack("Ccode/Cres/nsize", $attr_data);
            $attr['data'] = substr($packet, $pos + 4, $attr['size'] - 4);
            $this->data[$count] = $attr;
            $pos += $attr['size'];
            $count++;
        }
        $this->code = $fields['code'];
        $this->pos = 0;
        return TRUE;
    }

    function sendPacket($sock)
    {
        if ($this->code == 0) {
            return FALSE;
        }
        $size = 0;
        foreach ($this->data as $attr) {
            $size += $attr['size'];
        }
        $size += 4;
        $packet = pack("CCn", $this->code, 35, $size);
        foreach ($this->data as $attr) {
            $attr_data = pack("CCn", $attr['code'], $attr['res'], $attr['size']);
            $attr_data .= $attr['data'];
            $packet .= $attr_data;
        }
        $sock->write($packet);
        return TRUE;
    }

    function getAttr()
    {
        if ($this->pos >= count($this->data)) {
            return FALSE;
        }
        $attr = $this->data[$this->pos];
        $this->pos++;
        return $attr;
    }

    function putAttr($code, $data)
    {
        $attr = array();
        $attr['code'] = $code;
        $attr['data'] = $data;
        $attr['res'] = 0;
        $attr['size'] = strlen($data) + 4;
        $this->data[$this->pos] = $attr;
        $this->pos++;
        return TRUE;
    }

    function find($code)
    {
        foreach ($this->data as $attr) {
            if ($attr['code'] == $code) {
                return $attr;
            }
        }
        return FALSE;
    }
}