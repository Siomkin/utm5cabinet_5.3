<?php
    class Urfaphp_URFAClientPacket {
        var $version = 35;
        var $code;
        var $len;
        var $iterator = 0;
        var $attr=array();
        var $sock = false;
        var $data=array();

        function __construct($socket)
        {
            $this->clean();

            if (!empty($socket)) {
                $this->sock = $socket;
                //$this->read(); // ??? из-за этого места следующий read() стопорится, т.к. ждет данных
            } else {
                throw new Exception("wrong socket");
            }
        }

        function read()
        {
            $this->code = ord(fread($this->sock, 1));
            if ($this->version != ord(fread($this->sock, 1))) {
                throw new Exception("Error code " . ord(fread($this->sock, 1)));
            } else {
                list(, $this->len) = unpack("n", fread($this->sock, 2));
                $this->parse_packet_data();
            }
        }


        function parse_packet_data() 
        {
            $tmp_len = 4;
            while($tmp_len < $this->len)
            {
                list(, $code) = unpack("s", fread($this->sock, 2));
                list(, $length) = unpack("n", fread($this->sock, 2));
                $tmp_len += $length;
                if ($length == 4) {
                    $data = NULL;
                } else {
                    $data = fread($this->sock, $length - 4);
                }
                if ($code == 5) {
                    $this->data[] = $data;
                } else {
                    $this->attr[$code]['data'] = $data;
                    $this->attr[$code]['len'] = $length;
                }
            }
            //print "done read packet\n";
        }

        function clean() {
            $this->code = 0;
            $this->len = 4;
            $this->iterator = 0;
            $this->attr = array();
            $this->data = array();
        }

        function AttrSetString($str, $code)
        {
            $x = array();
            $this->attr[$code]['data'] = $str;
            $this->attr[$code]['len'] = strlen($str) + 4;
            $this->len += strlen($str) + 4;
        }

        function AttrSetInt($attr, $code)
        {
            $this->attr[$code]['data'] = pack("N",$attr);
            $this->attr[$code]['len'] = 8;
            $this->len += 8;
        }


        function AttrGetInt($code)
        {
            if (isset($this->attr[$code]['data'])) {
                $x = unpack("N", $this->attr[$code]['data']);
                if ($x[1]>2147483647) return $x[1]-4294967296;
                return $x[1];
            } else {
                return FALSE;
            }
        }


        function DataSetInt($param)
        {
            if($param=='') $param=0;
            $this->data[] = pack("N", $param);
            $this->len += 8;
        }
        function DataGetInt()
        {
            $num = $this->iterator;
            $this->iterator++;
            $tmp = unpack('Nval',$this->data[$num]);
            if ($tmp['val']>2147483647) return $tmp['val']-4294967296;
            return $tmp['val'];
        }

        function DataGetLong()
        {
            $num = $this->iterator;
            $this->iterator++;
            if(strlen($this->data[$num]) != 8) {
                return false;
            }
            $tmp = unpack('Ni1/Ni2',$this->data[$num]);
            $val = ($tmp['i2'] < 0 ? 0x100000000 + $tmp['i2'] : $tmp['i2']) + ($tmp['i1']*0x100000000);
            return $val;
        } 

        function DataSetLong($param)
        {
    	    echo "SetLong: not implemeted";
    	    return false;
/*
            $hi = base_convert(bcdiv($param,4294967296),10,16);
            $lo = base_convert(bcmod($param,4294967296),10,16);
            $this->data[] = pack('H*', $hi.$lo);
            $this->len += 12;
*/
        }

        function DataGetDouble()
        {
            $num = $this->iterator;
            $this->iterator++;
            if(strlen($this->data[$num]) != 8) {
                return false;
            }
            $tmp = unpack('Ni1/Ni2',$this->data[$num]);
            $tmp = pack('VV',$tmp['i2'],$tmp['i1']);
            $tmp = unpack('dval',$tmp);
            return $tmp['val'];
        }

        function DataSetDouble($param) { 
	    $str = pack('d', $param);
	    $str = strrev($str);
            $tmp = unpack('Ni1/Ni2', $str);
            $str = pack('NN',$tmp['i1'],$tmp['i2']);
            $this->data[]=$str; 
            $this->len += 12; 
        }

        function DataGetIP46Address()
        {
            $ret = array();
            $num = $this->iterator;
            $this->iterator++;

            $type = unpack("C",$this->data[$num]);
            //var_dump($type);
            $ret['addr'] = inet_ntop(substr($this->data[$num],1));
            $ret['type'] = $type[1];
            return $ret;
        } 


        function DataSetIP46Address($param)
        {
            //            var_dump($param);
            $addr = pack("C",$param['type']).inet_pton($param['addr']);
            $this->data[] = $addr;
            $this->len += strlen($addr) + 4;
        }

        function DataGetIPAddress()
        {
            $num = $this->iterator;
            $this->iterator++;
            return long2ip($this->bin2int($this->data[$num]) & 0xFFFFFFFF);
        }

        function DataSetIPAddress($param)
        {
            $this->data[] = pack("N", ip2long($param));
            $this->len += 8;
        }

        function DataGetString()
        {
            $num = $this->iterator;
            $this->iterator++;
            return $this->data[$num];
        }

        function DataSetString($str) {
            $this->data[] = $str;
            $this->len += strlen($str) + 4;
        }

        function write()
        {
            fwrite($this->sock, chr($this->code));
            fwrite($this->sock, chr($this->version));
            fwrite($this->sock, pack("n", $this->len));

            foreach ($this->attr as $code => $value) {
                fwrite($this->sock, pack("v", $code));
                fwrite($this->sock, pack("n", $value['len']));
                fwrite($this->sock, $value['data']);
            }

            foreach ($this->data as $code => $value) {
                fwrite($this->sock, pack("v", 5));
                fwrite($this->sock, pack("n", strlen($value) + 4));
                fwrite($this->sock, $value);
            }
        }

        function bin2int($data)
        {
            $x = unpack("N", $data);
            //if ($x[1]>2147483647) return $x[1]-4294967296;
            return $x[1];
        }

        function bin2double($data)
        {
            $x = unpack("d", strrev($data));
            return (double)$x[1];
        }


        function bin2long($data)
        {
            $arr = unpack('N2', $data);
            if (PHP_INT_SIZE == 4) {
                $hi = $arr[1];
                $lo = $arr[2];
                $isNeg = $hi  < 0;
                if ($isNeg) {
                    $hi = ~$hi & (int)0xffffffff;
                    $lo = ~$lo & (int)0xffffffff;

                    if ($lo == (int)0xffffffff) {
                        $hi++;
                        $lo = 0;
                    } else {
                        $lo++;
                    }
                }

                if ($hi & (int)0x80000000) {
                    $hi &= (int)0x7fffffff;
                    $hi += 0x80000000;
                }
                if ($lo & (int)0x80000000) {
                    $lo &= (int)0x7fffffff;
                    $lo += 0x80000000;
                }

                $value = bcmul($hi, 4294967296);
                $value = bcadd($value, $lo);

                if ($isNeg) {
                    $value = bcsub(0, $value);
                }
            } else {
                if ($arr[2] & 0x80000000) {
                    $arr[2] = $arr[2] & 0xffffffff;
                }
                if ($arr[1] & 0x80000000) {
                    $arr[1] = $arr[1] & 0xffffffff;
                    $arr[1] = $arr[1] ^ 0xffffffff;
                    $arr[2] = $arr[2] ^ 0xffffffff;
                    $arr[2] = $arr[2] - 1;
                    //$value = 0 - $arr[1]*4294967296 - $arr[2] - 1;
                    $value = bcmul($arr[1], 4294967296);
                    $value = bcsub(0, $value);
                    $value = bcsub($value, $arr[2]);

                } else {
                    //$value = $arr[1]*4294967296 + $arr[2];
                    $value = bcmul($arr[1], 4294967296);
                    $value = bcadd($value, $arr[2]);
                }
            }
            return $value;
        }
    }
?>
