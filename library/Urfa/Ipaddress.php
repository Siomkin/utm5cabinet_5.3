<?php

class Urfa_Ipaddress
{
    const IPv4 = 4;
    const IPv6 = 6;
    const Unknown = 0;
    private $data;
    private $type;

    public function __construct($Type, $Data)
    {
        $this->type = $Type;
        $this->data = $Data;
    }

    public static function fromString($AddressString)
    {
        $data = null;
        $type = null;
        switch (strlen($data = inet_pton($AddressString))) {
            case 4:
                $type = Urfa_Ipaddress::IPv4;
                break;
            case 16:
                $type = Urfa_Ipaddress::IPv6;
                break;
            default:
                // throw new IPaddressError("Invalid ip address type");
                throw new Urfa_Exception('Invalid ip address type.');
        }

        return new Urfa_Ipaddress($type, $data);
    }

    public function toString()
    {
        $rawStr = '';
        for ($i = 0; $i < count($this->data); $i++) {
            $rawStr .= chr($this->data[$i]);
        }
        return inet_ntop($rawStr);
    }

    public function toRaw()
    {
        return pack("C", $this->type) . $this->data;
    }
}

?>