<?php

class Urfa_Socket
{
    private $sock = NULL;
    private $host;
    private $port;
    private $admin;

    public function __construct($admin)
    {
        $this->admin = $admin;
    }

    function open($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $context = stream_context_create();

        if ($this->admin) {
            stream_context_set_option($context, 'ssl', 'capture_peer_cert', true);
            stream_context_set_option($context, 'ssl', 'local_cert', APPLICATION_PATH . '/../library/Urfa/admin.crt');
            stream_context_set_option($context, 'ssl', 'passphrase', 'netup');
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
        } else {
            stream_context_set_option($context, 'ssl', 'ciphers', 'ADH-RC4-MD5');
        }

        $this->sock = @stream_socket_client("tcp://[" . $host . "]:" . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
        if (!$this->sock) {
            $this->sock = @stream_socket_client("tcp://" . $host . ":" . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
            if ($this->sock)
                return true;

            return FALSE;
        }
        return TRUE;
    }

    function close()
    {
        if ($this->sock) {
            fclose($this->sock);
            $this->sock = NULL;
        }
        return TRUE;
    }

    function read($count)
    {
        return fread($this->sock, $count);
    }

    function write($var)
    {
        return fwrite($this->sock, $var);
    }

    function enable_crypto()
    {
        stream_socket_enable_crypto($this->sock, TRUE, STREAM_CRYPTO_METHOD_SSLv3_CLIENT);
    }
}
