<?php

class Urfaphp_URFAClientConnection
{
    private $socket = null;
    private $admin = false;

    public $error = ''; // Для возврата ошибки

    function __construct($address, $port, $login, $pass, $ssl = true, $admin = false)
    {
        $this->admin = $admin;
        if ($address && $port && $login) {
            if (!$this->open($address, $port)) {
                throw new Zend_Exception('Не возможно соединиться с биллингом', 500);

            }
            if (!$this->login($login, $pass, true)) {
                $this->error = 'login error';
            }
        }
    }

    function open($address, $port)
    {
        $context = stream_context_create(array('ssl' => array('ciphers' => "ADH-RC4-MD5",)));

        if ($this->admin) {
            $context = stream_context_create();
            stream_context_set_option($context, 'ssl', 'capture_peer_cert', true);
            stream_context_set_option($context, 'ssl', 'local_cert', APPLICATION_PATH . '/../library/Urfaphp/admin.crt');
            stream_context_set_option($context, 'ssl', 'passphrase', 'netup');
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
        } else {
            $context = stream_context_create(array('ssl' => array('ciphers' => "ADH-RC4-MD5",)));
        }

        $address = gethostbyname($address);
        $this->socket = stream_socket_client("tcp://$address:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        return !empty($this->socket); // Для возврата ошибки
    }


    function close()
    {
        return fclose($this->socket);
    }


    function login($login, $pass, $ssl = true)
    {
        $packet = $this->getPacket();
        while (!feof($this->socket)) {
            $packet->clean();
            $packet->read();

            switch ($packet->code) {
                case 192:
                    $this->urfa_auth($packet, $login, $pass, $ssl);
                    break;

                case 194:
                    $ssl = $packet->AttrGetInt(10);
                    if ($ssl) {
                        $this->ssl_connect($ssl);
                    }
                    return true;
                    break;

                case 195:
                    return false;
                    break;
            }
        }
    }


    function ssl_connect($ssl)
    {
        stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_SSLv3_CLIENT);
    }


    function urfa_call($code)
    {
        $packet = $this->getPacket();
        $packet->clean();
        $packet->code = 201;
        $packet->AttrSetInt($code, 3);
        $packet->write();

        if (!feof($this->socket)) {
            $packet->clean();
            $packet->read();
            switch ($packet->code) {
                case 200:
                    if ($packet->AttrGetInt(3) == $code)
                        return true;
                    else
                        return false;
            }
        }
    }


    function urfa_auth(&$packet, $uname, $upass, $ssl)
    {
        if ($this->admin)
            $ssl = 4;
        else
            $ssl = 2;
        $digest = $packet->attr[6]['data'];
        //    print "Received ".bin2hex($digest)." digest\n";
        $ctx = hash_init('md5');
        hash_update($ctx, $digest);
        hash_update($ctx, $upass);
        $hash = hash_final($ctx, true);
        //    print "Generate ".bin2hex($hash)." hash\n";
        $packet->clean();
        $packet->code = 193;
        $packet->AttrSetString($uname, 2);
        $packet->AttrSetString($digest, 8);
        $packet->AttrSetString($hash, 9);
        $packet->AttrSetInt($ssl, 10);
        $packet->AttrSetInt(2, 1);
        $packet->write();
    }

    /**
     * Читает данные из потока и возвращает их в URFAClient_Packet
     *
     * @return URFAClient_Packet
     */
    function urfa_get_data()
    {
        $packet = $this->getPacket();
        $packet->clean();
        while (true) {
            if (!feof($this->socket)) {
                $packet->read();
                if ($packet->AttrGetInt(4))
                    break;

            }
        }
        if (count($packet->data) == 0) return FALSE;
        return $packet;
    }

    function urfa_send_param(Urfaphp_URFAClientPacket $packet)
    {
        $packet->code = 200;
        $packet->write();
    }

    /**
     * Возвращает новый URFAClient_Packet
     *
     * @return Urfaphp_URFAClientPacket
     */
    public function getPacket()
    {
        return new Urfaphp_URFAClientPacket($this->socket);
    }

    public function __destruct()
    {
        $this->close();
    }
}

?>