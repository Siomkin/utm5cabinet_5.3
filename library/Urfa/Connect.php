<?php

define("RC_SESSION_INIT", 192);
define("RC_ACCESS_REQUEST", 193);
define("RC_ACCESS_ACCEPT", 194);
define("RC_ACCESS_REJECT", 195);
define("RC_SESSION_DATA", 200);
define("RC_SESSION_CALL", 201);
define("RC_SESSION_END", 203);

define("RA_USER_TYPE", 1);
define("RA_USER_NAME", 2);
define("RA_CALL", 3);
define("RA_END", 4);
define("RA_DATA", 5);
define("RA_KEY", 6);
define("RA_CLIENT_IP", 7);
define("RA_CHAP_CHALLENGE", 8);
define("RA_CHAP_RESPONSE", 9);
define("RA_SSL_REQUEST", 10);

define("RUT_USER", 0);
define("RUT_SERVICE", 1);
define("RUT_CARD", 2);

define("RSR_SSL_NONE", 0);
define("RSR_SSL_SSL3", 2);
define("RSR_SSL_CERT", 3);
define("RSR_SSL_SSL3_ADMIN", 4);
define("RSR_SSL_TLS1", 6);
define("RSR_SSL_AUTO",6);


define("URFA_STATE_NONE", 0);
define("URFA_STATE_INPUT", 1);
define("URFA_STATE_OUTPUT", 2);

class Urfa_Connect
{
    private $login = 'init';
    private $password = 'init';
    private $session_id = FALSE;
    private $client_ip = FALSE;
    private $sock = null;
    private $packet = FALSE;
    private $userType = RUT_SERVICE;
    private $sslType = RSR_SSL_AUTO;
    private $state = URFA_STATE_NONE;
    private $enableDebug = true;
    private $admin = false;


    /**
     * Создаём объект Urfa_Client
     */
    public function __construct($admin = false)
    {
        $this->admin = $admin;
    }

    public function __destruct()
    {
        $this->close_session(FALSE);
        $this->disconnect();
    }

    private function logger($str)
    {
        if ($this->enableDebug !== FALSE) {
            echo "<div><b>URFA error: </b>$str</div>";
        }
    }

    private function authorize()
    {
        $packet = new Urfa_Packet();
        if ($packet->recvPacket($this->sock) == FALSE) {
            $this->logger("auth: recvPacket failed 1");
            return FALSE;
        }
        if ($packet->getCode() != RC_SESSION_INIT) {
            $this->logger("auth: packet code != RC_SESSION_INIT");
            return FALSE;
        }
        $attr = $packet->getAttr();
        if ($attr['code'] != RA_KEY) {
            $this->logger("auth: attr code != RA_KEY");
            return FALSE;
        }
        if (strlen($attr['data']) != 16) {
            $this->logger("auth: key len != 16");
            return FALSE;
        }
        $key = $attr['data'];
        $packet->clear();
        $packet->setCode(RC_ACCESS_REQUEST);
        $packet->putAttr(RA_USER_NAME, $this->login);
        $md5_resp = md5($key . $this->password, TRUE);
        $packet->putAttr(RA_CHAP_RESPONSE, $md5_resp);
        $packet->putAttr(RA_CHAP_CHALLENGE, $key);
        if ($this->client_ip != FALSE) {
            $packet->putAttr(RA_CLIENT_IP, pack('V', $this->client_ip));
        }
        //restore session
        if ($this->session_id != FALSE && $this->client_ip != FALSE) {
            $packet->putAttr(RA_KEY, $this->session_id);
        }
        if ($this->userType != RUT_USER) {
            $data = pack("N", $this->userType);
            $packet->putAttr(RA_USER_TYPE, $data);
        }
        //if($this->session_id == false) {
        $data = pack("N", $this->sslType);
        $packet->putAttr(RA_SSL_REQUEST, $data);
        //}
        if ($packet->sendPacket($this->sock) == FALSE) {
            $this->logger("auth: sendPacket failed");
            return FALSE;
        }
        $packet->clear();
        if ($packet->recvPacket($this->sock) == FALSE) {
            $this->logger("auth: recvPacket failed 2");
            return FALSE;
        }
        if ($packet->getCode() != RC_ACCESS_ACCEPT) {
            return FALSE;
        }
        $attr = $packet->find(RA_SSL_REQUEST);
        if ($attr != FALSE) {
            $tmp = unpack('Nval', $attr['data']);

            if (defined('STREAM_CRYPTO_METHOD_ANY_CLIENT')) {
                $ssl_method = STREAM_CRYPTO_METHOD_ANY_CLIENT;
            } elseif (defined('STREAM_CRYPTO_METHOD_SSLv23_CLIENT')) {
                $ssl_method = STREAM_CRYPTO_METHOD_SSLv23_CLIENT;
            } elseif (defined('STREAM_CRYPTO_METHOD_TLS_CLIENT')) {
                $ssl_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            } elseif (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $ssl_method = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            } elseif (defined('STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT')) {
                $ssl_method = STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
            } elseif (defined('STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT')) {
                $ssl_method = STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT;
            } else {
                throw new ErrorException('All known crypto methods are undefined');
            }

            $this->sock->enable_crypto($ssl_method);

        }
        if ($this->session_id == FALSE) {
            $this->session_id = $key;
        }
        return TRUE;
    }


    public function get_key()
    {
        return bin2hex($this->session_id);
    }

    /**
     * Создаём подключение
     * @param $host
     * @param $port
     *
     * @return bool
     */
    public function connect($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->sock = new Urfa_Socket($this->admin);
        if ($this->sock->open($host, $port) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param      $login    Логин пользователя
     * @param      $password Пароль пользователя
     * @param bool $service
     * @param bool $client_ip
     *
     * @return bool
     */
    public function open_session($login, $password, $service = TRUE, $client_ip = FALSE)
    {
        $this->login = $login;
        $this->password = $password;
        $this->client_ip = ip2long($client_ip);
        $this->session_id = FALSE;
        if ($service) {
            $this->userType = RUT_SERVICE;
        } else {
            $this->userType = RUT_USER;
        }
        return $this->authorize();
    }

    /**
     * Восстановление сессии
     * @param $web_user Системный пользоваль utm
     * @param $web_pass пароль системного пользователя
     * @param $session_id
     * @param $client_ip
     *
     * @return bool
     */
    public function restore_session($web_user, $web_pass, $session_id, $client_ip)
    {
        $this->login = $web_user;
        $this->password = $web_pass;
        $this->session_id = pack('H32', $session_id);
        $this->client_ip = ip2long($client_ip);
        return $this->authorize();
    }

    public function close_session($drop = FALSE)
    {
        if ($this->sock == FALSE) {
            return FALSE;
        }
        $packet = new Urfa_Packet();
        $packet->setCode(RC_SESSION_END);
        if (!$drop) {
            $data = pack('N', 1);
        } else {
            $data = pack('N', 6);
        }
        $packet->putAttr(RA_END, $data);
        $packet->sendPacket($this->sock);
        // wait for the server close connection
        $packet->clear();
        $packet->recvPacket($this->sock);
        return TRUE;
    }

    public function disconnect()
    {
        if ($this->sock == FALSE) {
            return FALSE;
        }
        $this->sock->close();
        $this->sock = FALSE;
        $this->state = URFA_STATE_NONE;
        return TRUE;
    }

    public function call($fid)
    {
        if ($this->sock == FALSE) {
            $this->logger("call: socket closed");
            return FALSE;
        }
        if ($this->state != URFA_STATE_NONE) {
            $this->logger("call: state != URFA_STATE_NONE");
            $this->logger("call: state " . $this->state);
            return FALSE;
        }
        $packet = new Urfa_Packet();
        $packet->setCode(RC_SESSION_CALL);
        $data = pack("N", $fid);
        $packet->putAttr(RA_CALL, $data);
        if ($packet->sendPacket($this->sock) == FALSE) {
            $this->logger("call: sendPacket failed");
            return FALSE;
        }
        $packet->clear();
        if ($packet->recvPacket($this->sock) == FALSE) {
            $this->logger("call: recvPacket failed 3");
            return FALSE;
        }
        if ($packet->getCode() != RC_SESSION_DATA) {
            $this->logger("call: packet code != RC_SESSION_DATA");
            return FALSE;
        }
        $attr = $packet->getAttr();
        if ($attr['code'] != RA_CALL) {
            // public function call not permitted
            if ($attr['code'] == RA_END) {
                return 0;
            }
            $this->logger("call: attr code != RA_CALL (" . $attr['code'] . ")");
            return FALSE;
        }
        $this->packet = new Urfa_Packet();
        $this->packet->setCode(RC_SESSION_DATA);
        $this->state = URFA_STATE_INPUT;
        return $fid;
    }

    public function send()
    {
        if ($this->state != URFA_STATE_INPUT) {
            $this->logger("send: state != URFA_STATE_INPUT");
            return FALSE;
        }
        if ($this->packet->size() == 0) {
            $this->state = URFA_STATE_OUTPUT;
            return TRUE;
        }
        if ($this->packet->sendPacket($this->sock) == FALSE) {
            $this->logger("send: sendPacket failed");
            return FALSE;
        }
        $this->state = URFA_STATE_OUTPUT;
        $this->packet = FALSE;
        return TRUE;
    }

    public function finish()
    {
        if ($this->state != URFA_STATE_OUTPUT) {
            $this->logger("finish: state != URFA_STATE_OUTPUT");
            return FALSE;
        }
        if ($this->packet) {
            while ($this->packet->find(RA_END) == FALSE) {
                if ($this->packet->recvPacket($this->sock) == FALSE) {
                    break;
                }
            }
        }
        $this->packet = FALSE;
        $this->state = URFA_STATE_NONE;
        return TRUE;
    }

    private function put($data)
    {
        if ($this->state != URFA_STATE_INPUT) {
            $this->logger("put: state != URFA_STATE_INPUT");
            return FALSE;
        }
        if ($this->packet == FALSE) {
            $this->packet = new Urfa_Packet;
            $this->packet->setCode(RC_SESSION_DATA);
        }
        $this->packet->putAttr(RA_DATA, $data);
        return TRUE;
    }

    private function get()
    {
        if ($this->state != URFA_STATE_OUTPUT) {
            $this->logger("get: state != URFA_STATE_OUTPUT");
            return FALSE;
        }
        if ($this->packet != FALSE && $this->packet->getPos() >= $this->packet->size()) {
            $this->packet = FALSE;
        }
        if ($this->packet == FALSE) {
            $this->packet = new Urfa_Packet();
            if ($this->packet->recvPacket($this->sock) == FALSE) {
                $this->logger("get: recvPacket failed 4");
                return FALSE;
            }
            if ($this->packet->getCode() != RC_SESSION_DATA) {
                $this->logger("get: packet code != RC_SESSION_DATA");
                return FALSE;
            }
        }
        $attr = $this->packet->getAttr();
        if ($attr == FALSE) {
            $this->logger("get: getAttr failed");
            return FALSE;
        }

        if ($attr['code'] != RA_DATA) {
            $this->logger("get: attr code != RA_DATA");
            return FALSE;
        }
        return $attr;
    }

    public function put_int($i)
    {
        $data = pack('N', $i);
        return $this->put($data);
    }

    public function put_long($l)
    {
        $this->logger("put_long: not implemeted");
        return FALSE;
    }


    /* Convert float from HostOrder to Network Order */
    public function FToN($val)
    {
        $a = unpack("I", pack("f", $val));
        return pack("N", $a[1]);
    }

    /* Convert float from Network Order to HostOrder */
    public function NToF($val)
    {
        $a = unpack("N", $val);
        $b = unpack("f", pack("I", $a[1]));
        return $b[1];
    }

    public function put_double($d)
    {
        $str = pack('d', $d);
        $str = strrev($str);
        $tmp = unpack('Ni1/Ni2', $str);
        $str = pack('NN', $tmp['i1'], $tmp['i2']);

        return $this->put($str);
    }

    public function put_string($s)
    {
        return $this->put($s);
    }

    public function put_ip_address($addr)
    {
        return $this->put($addr->toRaw());

    }


    public function get_int()
    {
        $attr = $this->get();
        if ($attr == FALSE) {
            return FALSE;
        }
        $tmp = unpack('Nval', $attr['data']);
        return $tmp['val'];
    }

    public function get_long()
    {
        $attr = $this->get();
        if ($attr == FALSE) {
            return FALSE;
        }
        if (strlen($attr['data']) != 8) {
            $this->logger("get_long: size != 8");
            return FALSE;
        }
        $tmp = unpack('Ni1/Ni2', $attr['data']);
        $val = ($tmp['i2'] < 0 ? 0x100000000 + $tmp['i2'] : $tmp['i2']) + ($tmp['i1'] * 0x100000000);
        return $val;
    }

    public function get_double()
    {
        $attr = $this->get();
        if ($attr == FALSE) {
            return FALSE;
        }
        if (strlen($attr['data']) != 8) {
            $this->logger("get_double: size != 8");
            return FALSE;
        }

        $tmp = unpack('Ni1/Ni2', $attr['data']);
        $tmp = pack('VV', $tmp['i2'], $tmp['i1']);
        $tmp = unpack('dval', $tmp);
        return $tmp['val'];
    }

    public function get_string()
    {
        $attr = $this->get();
        if ($attr == FALSE) {
            return FALSE;
        }
        return $attr['data'];
    }

    public function get_ip_address()
    {
        $attr = $this->get();
        if ($attr == false) {
            return false;
        }
        $data = array_merge(unpack("C*", $attr["data"]));
        return new Urfa_Ipaddress($data[0], array_slice($data, 1));
    }


    public function get_state()
    {
        return $this->state;
    }
}