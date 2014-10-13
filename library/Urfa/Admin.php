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

        $this->urfa->finish();
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

        $this->urfa->finish();
    }


    /*
    <function name="rpcf_get_groups_for_user" id="0x2550">
        <input>
          <integer name="user_id" />
        </input>
        <output>
          <integer name="groups_size" />
          <for name="i" from="0" count="groups_size">
            <integer name="group_id" array_index="i" />
            <string name="group_name" array_index="i" />
          </for>
        </output>
    </function>
     */
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
        $ret['groups_size'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $group['group_id'] = $this->urfa->get_int();
            $group['group_name'] = $this->urfa->get_string();
            $ret['group'][] = $group;
        }

        $this->urfa->finish();

        return $ret;
    }

    /*
    <function name="rpcf_liburfa_list" id="0x0040">
        <input/>
        <output>
          <integer name="size" />
          <for name="i" from="0" count="size">
            <string name="module" array_index="i" />
            <string name="version" array_index="i" />
            <string name="path" array_index="i" />
          </for>
        </output>
      </function>
    */

    /**
     * @return array|bool
     */
    function rpcf_liburfa_list()
    { //0x0040
        $ret = array();
        if (!$this->urfa->call(0x0040)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $this->urfa->send();
        $size = $this->urfa->get_int();
        $ret['size'] = $size;
        for ($i = 0; $i < $size; $i++) {
            $list['module'] = $this->urfa->get_string();
            $list['version'] = $this->urfa->get_string();
            $list['path'] = $this->urfa->get_string();
            $ret['list'][] = $list;
        }

        $this->urfa->finish();

        return $ret;
    }

    /*
    <function name="rpcf_liburfa_symtab" id="0x0044">
        <input/>
        <output>
            <integer name="size" />
            <for name="i" from="0" count="size">
                <integer name="id" array_index="i" />
                <string name="name" array_index="i" />
                <string name="module" array_index="i" />
            </for>
        </output>
    </function>
    */

    /**
     * @return array|bool
     */
    function rpcf_liburfa_symtab()
    { //0x0044
        $ret = array();
        if (!$this->urfa->call(0x0044)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $this->urfa->send();
        $size = $this->urfa->get_int();
        $ret['size'] = $size;
        for ($i = 0; $i < $size; $i++) {
            $list['id'] = $this->urfa->get_int();
            $list['name'] = $this->urfa->get_string();
            $list['module'] = $this->urfa->get_string();
            $ret['symtab'][] = $list;
        }

        $this->urfa->finish();

        return $ret;
    }

    /*
    <function name="rpcf_core_version" id="0x0045">
        <input/>
        <output>
            <string name="core_version"/>
        </output>
    </function>
    */

    /**
     * Версия биллинга (Например: 5)
     * @return string
     */
    function rpcf_core_version()
    { //0x0045

        if (!$this->urfa->call(0x0045)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $this->urfa->send();
        $ret = $this->urfa->get_string();

        $this->urfa->finish();

        return $ret;
    }

    /*
    <function name="rpcf_core_build" id="0x0046">
        <input/>
        <output>
            <string name="build" />
        </output>
    </function>
     */

    /**
     * Версия билда (Например: 002)
     * @return string
     */
    function rpcf_core_build()
    { //0x0046
        if (!$this->urfa->call(0x0046)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $this->urfa->send();
        $ret = $this->urfa->get_string();

        $this->urfa->finish();

        return $ret;
    }
}