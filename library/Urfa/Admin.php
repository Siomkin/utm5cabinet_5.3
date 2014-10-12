<?php

class Urfa_Admin
{
    protected $urfa;

    protected $config;

    function __construct($host = null, $port = null, $ssl = true, $admin = true)
    {
        if (is_null($host) || is_null($port)) {
            $this->config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/billing.ini', 'app');
            $host = $this->config->urfaphp->host;
            $port = $this->config->urfaphp->port;
        }
        $this->urfa = new Urfa_Connect($admin);
        if (!$this->urfa->connect($host, $port, $ssl)) {
            throw new Urfa_Exception('Не возможно подключиться к биллингу. Попробуйте войти позже.');
        }
        $this->open_session();
    }

    /**
     *
     * @param bool $service
     * @param bool $client_ip
     * @return bool
     * @throws Urfa_Exception
     */
    public function open_session($service = true, $client_ip = FALSE)
    {
        $open_session = $this->urfa->open_session(
            $this->config->urfaphp->login,
            $this->config->urfaphp->password,
            $service,
            $client_ip
        );
        if (!$open_session) {
            throw new Urfa_Exception('Не возможно открыть сессию', 500);
        }
        return $open_session;
    }

    public function close_session()
    {
        $this->urfa->close_session();
    }


    function __destruct()
    {
        unset($this->urfa);
    }

    ////////////////////////////////////////////
    ////------- Админские функции ----------////
    ////////////////////////////////////////////


    /**
     * Добавить группу пользователю
     * @param $user_id
     * @param $group_id
     * @return bool
     */
    function rpcf_add_group_to_user($user_id, $group_id)
    { //0x2552
        if (!$this->urfa->call(0x2552)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $this->urfa->put_int($user_id);
        $this->urfa->put_int($group_id);
        $this->urfa->send();
    }

    /**
     * Удалить группу для пользователя
     * @param $user_id
     * @param $group_id
     * @return bool
     */
    function rpcf_remove_user_from_group($user_id, $group_id)
    { //0x2408

        if (!$this->urfa->call(0x2408)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }

        $this->urfa->put_int($user_id);
        $this->urfa->put_int($group_id);
        $this->urfa->send();
    }

    /**
     * Получение списка групп пользователя
     * @param $user_id
     * @return array|bool
     */
    function rpcf_get_groups_for_user($user_id)
    { //0x2550
        $ret = array();
        if (!$this->urfa->call(0x2550)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }

        $this->urfa->put_int($user_id);
        $this->urfa->send();
        $count = $this->urfa->get_int();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $group['group_id'] = $this->urfa->get_int();
            $group['group_name'] = $this->urfa->get_string();
            $ret['group'][] = $group;
        }
        return $ret;
    }


}