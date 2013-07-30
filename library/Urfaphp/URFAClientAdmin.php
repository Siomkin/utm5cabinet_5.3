<?php

class Urfaphp_URFAClientAdmin extends Urfaphp_URFAClient
{
    /**
     * Возвращает объект Urfaphp_URFAClient_User5, используя текущие настройки подключения
     * @return Urfaphp_URFAClient_User5
     */
    public function getURFAClient_User5($login, $pass, $ssl = true)
    {
        return new Urfaphp_URFAClientUser5($login, $pass, $this->address, $this->port, $ssl);
    }

    /**
     * Возвращает информацию об использованном пользователем трафике, согласно выбранному типу группировки
     * @param $user_id
     * @param $time_start
     * @param $time_end
     * @param $type
     * 1 - Отчет с группировкой по часам
     * 2 - Отчет с группировкой по дням
     * 3 - Общий отчет
     * 4 - Отчет с группировкой по IP
     * @return array|bool
     */
    function rpcf_traffic_report_ex($user_id, $time_start, $time_end, $type)
    {   //0x3009
        $ret = array();
        if (!$this->connection->urfa_call(0x3009)) {
            throw new Zend_Controller_Exception('Error calling function ' . __FUNCTION__,503);
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($type);
        $packet->DataSetInt($user_id);
        $packet->DataSetInt(0);
        $packet->DataSetInt(0);
        $packet->DataSetInt(0);
        $packet->DataSetInt($time_start);
        $packet->DataSetInt($time_end);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['bytes_in_kbyte'] = $x->DataGetDouble();
            $users_count = $x->DataGetInt();
            $ret['users_count'] = $users_count;
            $traffic = array();
            for ($i = 0; $i < $users_count; $i++) {
                $atr_size = $x->DataGetInt();
                $traffic['atr_size'] = $atr_size;
                $ips = array();
                for ($j = 0; $j < $atr_size; $j++) {
                    $ips['account_id'] = $x->DataGetInt();
                    $ips['login'] = $x->DataGetString();
                    $ips['discount_date'] = $x->DataGetInt();
                    $ips['tclass'] = $x->DataGetInt();
                    $ips['base_cost'] = $x->DataGetDouble();
                    $ips['bytes'] = $x->DataGetLong();
                    $ips['discount'] = $x->DataGetDouble();
                    $traffic['ips'][$j] = $ips;
                }
                $ret['traffic'][$i] = $traffic;
            }
        }
        return $ret;
    }

    function rpcf_get_tariffs_list()
    {   //0x3010
        $ret = array();
        if (!$this->connection->urfa_call(0x3010)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data(); // Tariff count
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $x = $this->connection->urfa_get_data();
            $tariff['id'] = $x->DataGetInt();
            $tariff['name'] = $x->DataGetString();
            $tariff['create_date'] = $x->DataGetInt();
            $tariff['who_create'] = $x->DataGetInt();
            $tariff['login'] = $x->DataGetString();
            $tariff['change_create'] = $x->DataGetInt();
            $tariff['who_change'] = $x->DataGetInt();
            $tariff['login_change'] = $x->DataGetString();
            $tariff['expire_date'] = $x->DataGetInt();
            $tariff['is_blocked'] = $x->DataGetInt();
            $tariff['balance_rollover'] = $x->DataGetInt();
            $ret['tariffs'][] = $tariff;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_core_version()
    {   //0x0045
        $ret = array();
        if (!$this->connection->urfa_call(0x0045)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data();
        $ret['core_version'] = $x->DataGetString();
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_core_build()
    {   //0x0046
        $ret = array();
        if (!$this->connection->urfa_call(0x0046)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data();
        $ret['core_build'] = $x->DataGetString();
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_discount_periods()
    {   //0x2600
        $ret = array();
        if (!$this->connection->urfa_call(0x2600)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data(); //Periods count
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $x = $this->connection->urfa_get_data();
            $period['static_id'] = $x->DataGetInt();
            $period['discount_period_id'] = $x->DataGetInt();
            $period['start_date'] = $x->DataGetInt();
            $period['end_date'] = $x->DataGetInt();
            $period['periodic_type'] = $x->DataGetInt();
            $period['custom_duration'] = $x->DataGetInt();
            $period['next_discount_period_id'] = $x->DataGetInt();
            $period['canonical_length'] = $x->DataGetInt();
            $ret['discount_periods'][] = $period;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_bytes_in_kb()
    { //0x10002
        $ret = array();
        if (!$this->connection->urfa_call(0x10002)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data();
        $ret['bytes_in_kb'] = $x->DataGetInt();
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_currency_list()
    { //0x2910
        $ret = array();
        if (!$this->connection->urfa_call(0x2910)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data();
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $x = $this->connection->urfa_get_data();
            $currency['id'] = $x->DataGetInt();
            $currency['currency_brief_name'] = $x->DataGetString();
            $currency['currency_full_name'] = $x->DataGetString();
            $currency['percent'] = $x->DataGetDouble();
            $currency['rates'] = $x->DataGetDouble();
            $ret['currency'][] = $currency;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_payment_methods_list()
    { //0x3100
        $ret = array();
        if (!$this->connection->urfa_call(0x3100)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data();
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $list['id'] = $x->DataGetInt();
            $list['name'] = $x->DataGetString();
            $ret['payments_methods'][] = $list;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_userinfo($user_id)
    { //0x2006
        $ret = array();
        if (!$this->connection->urfa_call(0x2006)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $this->connection->urfa_send_param($packet);
        $x = $this->connection->urfa_get_data();
        $user = $x->DataGetInt();
        $ret['user_id'] = $user;
        if ($user != 0) {
            $ret['user_id'] = $user;
            $accounts_count = $x->DataGetInt();
            $ret['accounts_count'] = $accounts_count;
            for ($i = 0; $i < $accounts_count; $i++) {
                $accounts['id'] = $x->DataGetInt();
                $accounts['name'] = $x->DataGetString();
                $ret['accounts'][] = $accounts;
            }
            $ret['login'] = $x->DataGetString();
            $ret['password'] = $x->DataGetString();
            $ret['basic_account'] = $x->DataGetInt();
            $ret['full_name'] = $x->DataGetString();
            $ret['create_date'] = $x->DataGetInt();
            $ret['last_change_date'] = $x->DataGetInt();
            $ret['who_create'] = $x->DataGetInt();
            $ret['who_change'] = $x->DataGetInt();
            $ret['is_juridical'] = $x->DataGetInt();
            $ret['jur_address'] = $x->DataGetString();
            $ret['act_address'] = $x->DataGetString();
            $ret['work_tel'] = $x->DataGetString();
            $ret['home_tel'] = $x->DataGetString();
            $ret['mob_tel'] = $x->DataGetString();
            $ret['web_page'] = $x->DataGetString();
            $ret['icq_number'] = $x->DataGetString();
            $ret['tax_number'] = $x->DataGetString();
            $ret['kpp_number'] = $x->DataGetString();
            $ret['bank_id'] = $x->DataGetInt();
            $ret['bank_account'] = $x->DataGetString();
            $ret['comments'] = $x->DataGetString();
            $ret['personal_manager'] = $x->DataGetString();
            $ret['connect_date'] = $x->DataGetInt();
            $ret['email'] = $x->DataGetString();
            $ret['is_send_invoice'] = $x->DataGetInt();
            $ret['advance_payment'] = $x->DataGetInt();
            $ret['house_id'] = $x->DataGetInt();
            $ret['flat_number'] = $x->DataGetString();
            $ret['entrance'] = $x->DataGetString();
            $ret['floor'] = $x->DataGetString();
            $ret['district'] = $x->DataGetString();
            $ret['building'] = $x->DataGetString();
            $ret['passport'] = $x->DataGetString();
            $ret['parameters_count'] = $x->DataGetInt();
            for ($i = 0; $i < $ret['parameters_size']; $i++) {
                $parameters['id'] = $x->DataGetInt();
                $parameters['value'] = $x->DataGetString();
                $ret['parameters'][] = $parameters;
            }
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_ipgroups_list()
    { //0x2900
        $ret = array();
        if (!$this->connection->urfa_call(0x2900)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data();
        $groups_count = $x->DataGetInt();
        $ret['groups_count'] = $groups_count;
        for ($i = 0; $i < $groups_count; $i++) {
            $x = $this->connection->urfa_get_data();
            $count = $x->DataGetInt();
            for ($j = 0; $j < $count; $j++) {
                $x = $this->connection->urfa_get_data();
                $group['id'] = $x->DataGetInt();
                $group['ip'] = $x->DataGetIPAddress();
                $group['mask'] = $x->DataGetIPAddress();
                $group['mac'] = $x->DataGetString();
                $group['login'] = $x->DataGetString();
                $group['allowed_cid'] = $x->DataGetString();
                $groups['group'][] = $group;
            }
            $groups['group_count'] = $count;
            $ret['groups'][] = $groups;
            unset($groups);
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_tclass($class_id)
    { //0x2302
        $ret = array();
        if (!$this->connection->urfa_call(0x2302)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($class_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['tclass_name'] = $x->DataGetString();
            $ret['graph_color'] = $x->DataGetInt();
            $ret['is_display'] = $x->DataGetInt();
            $ret['is_fill'] = $x->DataGetInt();
            $ret['time_range_id'] = $x->DataGetInt();
            $ret['dont_save'] = $x->DataGetInt();
            $ret['local_traf_policy'] = $x->DataGetInt();
            $ret['tclass_count'] = $x->DataGetInt();
            $count = $ret['tclass_count'];
            for ($i = 0; $i < $count; $i++) {
                $x = $this->connection->urfa_get_data();
                $tclass['saddr'] = $x->DataGetIPAddress();
                $tclass['saddr_mask'] = $x->DataGetIPAddress();
                $tclass['sport'] = $x->DataGetInt();
                $tclass['input'] = $x->DataGetInt();
                $tclass['src_as'] = $x->DataGetIPAddress();
                $tclass['daddr'] = $x->DataGetIPAddress();
                $tclass['daddr_mask'] = $x->DataGetIPAddress();
                $tclass['dport'] = $x->DataGetInt();
                $tclass['output'] = $x->DataGetInt();
                $tclass['dst_as'] = $x->DataGetIPAddress();
                $tclass['proto'] = $x->DataGetInt();
                $tclass['tos'] = $x->DataGetInt();
                $tclass['nexthop'] = $x->DataGetInt();
                $tclass['tcp_flags'] = $x->DataGetInt();
                $tclass['ip_from'] = $x->DataGetIPAddress();
                $tclass['use_sport'] = $x->DataGetInt();
                $tclass['use_input'] = $x->DataGetInt();
                $tclass['use_src_as'] = $x->DataGetInt();
                $tclass['use_dport'] = $x->DataGetInt();
                $tclass['use_output'] = $x->DataGetInt();
                $tclass['use_dst_as'] = $x->DataGetInt();
                $tclass['use_proto'] = $x->DataGetInt();
                $tclass['use_tos'] = $x->DataGetInt();
                $tclass['use_nexthop'] = $x->DataGetInt();
                $tclass['use_tcp_flags'] = $x->DataGetInt();
                $tclass['skip'] = $x->DataGetInt();
                $ret['tclass'][] = $tclass;
            }
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_get_accountinfo($account_id)
    { //0x2030
        $ret = array();
        if (!$this->connection->urfa_call(0x2030)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($account_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['unused'] = $x->DataGetInt();
            $ret['is_blocked'] = $x->DataGetInt();
            $ret['dealer_account_id'] = $x->DataGetInt();
            $ret['is_dealer'] = $x->DataGetInt();
            $ret['vat_rate'] = $x->DataGetDouble();
            $ret['sale_tax_rate'] = $x->DataGetDouble();
            $ret['comission_coefficient'] = $x->DataGetDouble();
            $ret['default_comission_value'] = $x->DataGetDouble();
            $ret['credit'] = $x->DataGetDouble();
            $ret['balance'] = $x->DataGetDouble();
            $ret['int_status'] = $x->DataGetInt();
            $ret['block_recalc_abon'] = $x->DataGetInt();
            $ret['block_recalc_prepaid'] = $x->DataGetInt();
            $ret['unlimited'] = $x->DataGetInt();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_get_user_account_list($user_id)
    { //0x2033
        $ret = array();
        if (!$this->connection->urfa_call(0x2033)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $count = $x->DataGetInt();
            $ret['count'] = $count;
            for ($i = 0; $i < $count; $i++) {
                $account['id'] = $x->DataGetInt();
                $account['name'] = $x->DataGetString();
                $ret['accounts'][] = $account;
            }
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_block_account($account_id, $block)
    { //0x2037
        $ret = array();
        if (!$this->connection->urfa_call(0x2037)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($account_id);
        $packet->DataSetInt($block);
        $this->connection->urfa_send_param($packet);
        $this->connection->urfa_get_data();
    }

    function rpcf_get_tclasses()
    { //0x2300
        $ret = array();
        if (!$this->connection->urfa_call(0x2300)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $x = $this->connection->urfa_get_data();
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $x = $this->connection->urfa_get_data();
            $tclass['id'] = $x->DataGetInt();
            $tclass['name'] = $x->DataGetString();
            $tclass['graph_color'] = $x->DataGetInt();
            $tclass['is_display'] = $x->DataGetInt();
            $tclass['is_fill'] = $x->DataGetInt();
            $ret['tclasses'][] = $tclass;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_general_report_new($user_id = 0, $account_id = 0, $group_id = 0,
        $discount_period_id = 0, $start_date, $end_date)
    { //0x3020
        $ret = array();
        if (!$this->connection->urfa_call(0x3020)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $packet->DataSetInt($account_id);
        $packet->DataSetInt($group_id);
        $packet->DataSetInt($discount_period_id);
        $packet->DataSetInt($start_date);
        $packet->DataSetInt($end_date);
        $this->connection->urfa_send_param($packet);
        $x = $this->connection->urfa_get_data();
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $x = $this->connection->urfa_get_data();
            $rep['account_id'] = $x->DataGetInt();
            $rep['login'] = $x->DataGetString();
            $rep['incoming_rest'] = $x->DataGetDouble();
            $rep['discounted_once'] = $x->DataGetDouble();
            $rep['discounted_periodic'] = $x->DataGetDouble();
            $rep['discounted_iptraffic'] = $x->DataGetDouble();
            $rep['discounted_hotspot'] = $x->DataGetDouble();
            $rep['discounted_dialup'] = $x->DataGetDouble();
            $rep['discounted_telephony'] = $x->DataGetDouble();
            $rep['tax'] = $x->DataGetDouble();
            $rep['discounted_with_tax'] = $x->DataGetDouble();
            $rep['payments'] = $x->DataGetDouble();
            $rep['outgoing_rest'] = $x->DataGetDouble();
            $ret['report'][] = $rep;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    ///////////////////////////////////////////////////////////////
    function rpcf_add_to_ipgroup($id, $ip, $mask, $login = "", $pass = "",
        $mac = "", $cid = "")
    { //0x5200
        $ret = array();
        if (!$this->connection->urfa_call(0x5200)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($id);
        $packet->DataSetIPAddress($ip);
        $packet->DataSetIPAddress($mask);
        $packet->DataSetString($login);
        $packet->DataSetString($pass);
        $packet->DataSetString($mac);
        $packet->DataSetString($cid);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $code = $x->DataGetInt();
        }
        $this->connection->urfa_get_data();
        // -1 Error (bug in api.xml - 0)
        return $code;
    }

    function rpcf_delete_from_ipgroup_by_ipgroup($id, $ip, $mask)
    { //0x5102
        $ret = array();
        if (!$this->connection->urfa_call(0x5102)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($id);
        $packet->DataSetIPAddress($ip);
        $packet->DataSetIPAddress($mask);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $code = $x->DataGetInt();
        }
        $this->connection->urfa_get_data();
        // 0 Error
        return $code;
    }

    function rpcf_get_all_services_for_user($account_id)
    { //0x2700
        $ret = array();
        if (!$this->connection->urfa_call(0x2700)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($account_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $count = $x->DataGetInt();
            $ret['count'] = $count;
            for ($i = 0; $i < $count; $i++) {
                $x = $this->connection->urfa_get_data();
                $service['id'] = $x->DataGetInt();
                if ($service['id'] != -1) {
                    $service['type'] = $x->DataGetInt();
                    $service['name'] = $x->DataGetString();
                    $service['tarif_name'] = $x->DataGetString();
                    $service['cost'] = $x->DataGetDouble();
                    $service['slink_id'] = $x->DataGetInt();
                    $service['period'] = $x->DataGetInt();
                } else {
                    $service['type'] = -1;
                    $service['name'] = "";
                    $service['tarif_name'] = "";
                    $service['cost'] = -1;
                    $service['slink_id'] = -1;
                    $service['period'] = -1;
                }
                $ret['services'][] = $service;
            }
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_remove_user_from_group($user_id, $group_id)
    { //0x2408
        if (!$this->connection->urfa_call(0x2408)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $packet->DataSetInt($group_id);
        $this->connection->urfa_send_param($packet);
        $this->connection->urfa_get_data();
    }

    function rpcf_add_group_to_user($user_id, $group_id)
    { //0x2552
        if (!$this->connection->urfa_call(0x2552)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $packet->DataSetInt($group_id);
        $this->connection->urfa_send_param($packet);
        $this->connection->urfa_get_data();
    }

    function rpcf_get_user_tariffs($user_id, $account_id = 0)
    { //0x3017
        $ret = array();
        if (!$this->connection->urfa_call(0x3017)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $packet->DataSetInt($account_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $count = $x->DataGetInt();
            $ret['count'] = $count;
            for ($i = 0; $i < $count; $i++) {
                $tariff['current_tariff'] = $x->DataGetInt();
                $tariff['next_tariff'] = $x->DataGetInt();
                $tariff['discount_period_id'] = $x->DataGetInt();
                $tariff['tariff_link_id'] = $x->DataGetInt();
                $ret['user_tariffs'][] = $tariff;
            }
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_delete_from_ipgroup($slink_id, $ip, $mask = "255.255.255.255")
    { //0x5101
        if (!$this->connection->urfa_call(0x5101)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($slink_id);
        $packet->DataSetIPAddress($ip);
        $packet->DataSetIPAddress($mask);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $code = $x->DataGetInt();
            $this->connection->urfa_get_data();
        } else {
            return -1; // invalid slink_id
        }
        // 0 delete error
        return $code;
    }

    function rpcf_get_iptraffic_service_link($slink_id)
    { //0x2702
        $ret = array();
        if (!$this->connection->urfa_call(0x2702)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($slink_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['tariff_link_id'] = $x->DataGetInt();
            $ret['is_blocked'] = $x->DataGetInt();
            $ret['discount_period_id'] = $x->DataGetInt();
            $ret['start_date'] = $x->DataGetInt();
            $ret['expire_date'] = $x->DataGetInt();
            $ret['unabon'] = $x->DataGetInt();
            $ret['unprepay'] = $x->DataGetInt();
            $ret['tariff_id'] = $x->DataGetInt();
            $ret['parent_id'] = $x->DataGetInt();
            $ret['ip_groups_count'] = $x->DataGetInt();
            for ($i = 0; $i < $ret['ip_groups_count']; $i++) {
                $ipgroup['ip'] = $x->DataGetIPAddress();
                $ipgroup['mask'] = $x->DataGetIPAddress();
                $ipgroup['mac'] = $x->DataGetString();
                $ipgroup['login'] = $x->DataGetString();
                $ipgroup['password'] = $x->DataGetString();
                $ipgroup['allowed_cid'] = $x->DataGetString();
                $ipgroup['not_vpn'] = $x->DataGetInt();
                $ipgroup['dont_use_fw'] = $x->DataGetInt();
                $ipgroup['router_id'] = $x->DataGetInt();
                $ret['ip_groups'][] = $ipgroup;
            }
            $ret['quotas_count'] = $x->DataGetInt();
            for ($i = 0; $i < $ret['quotas_count']; $i++) {
                $quota['router_id'] = $x->DataGetInt();
                $quota['tclass_name'] = $x->DataGetString();
                $quota['quota'] = $x->DataGetLong();
                $ret['quotas'][] = $quota;
            }
            $this->connection->urfa_get_data();
        } else {
            return -1; // invalid slink_id
        }
        return $ret;
    }

    function rpcf_get_groups_list($user_id = 0)
    { //0x2400
        $ret = array();
        if (!$this->connection->urfa_call(0x2400)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $this->connection->urfa_send_param($packet);
        $x = $this->connection->urfa_get_data();
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $x = $this->connection->urfa_get_data();
            $group['group_id'] = $x->DataGetInt();
            $group['group_name'] = $x->DataGetString();
            $ret['group'][] = $group;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_groups_for_user($user_id)
    { //0x2550
        $ret = array();
        if (!$this->connection->urfa_call(0x2550)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $this->connection->urfa_send_param($packet);
        $x = $this->connection->urfa_get_data();
        $count = $x->DataGetInt();
        $ret['count'] = $count;
        for ($i = 0; $i < $count; $i++) {
            $group['group_id'] = $x->DataGetInt();
            $group['group_name'] = $x->DataGetString();
            $ret['group'][] = $group;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    function rpcf_get_new_secret($len = 8)
    { //0x0060
        $ret = array();
        if (!$this->connection->urfa_call(0x0060)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($len);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['error'] = $x->DataGetString();
            $ret['secret'] = $x->DataGetString();
            $this->connection->urfa_get_data();
        }
        // 0 Error
        return $ret;
    }

    // return user_id or 0 if user not found
    function rpcf_get_user_by_account($account_id)
    { //0x2026
        $ret = array();
        if (!$this->connection->urfa_call(0x2026)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($account_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $user_id = $x->DataGetInt();
        }
        $this->connection->urfa_get_data();
        return $user_id;
    }

    function rpcf_get_discount_period($period_id)
    { //0x2602
        $ret = array();
        if (!$this->connection->urfa_call(0x2602)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($period_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['start_date'] = $x->DataGetInt();
            $ret['end_date'] = $x->DataGetInt();
            $ret['periodic_type'] = $x->DataGetInt();
            $ret['custom_duration'] = $x->DataGetInt();
            $ret['discounts_per_week'] = $x->DataGetInt();
            $ret['next_discount_period_id'] = $x->DataGetInt();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_get_tariff($tariff_id)
    { //0x3011
        $ret = array();
        if (!$this->connection->urfa_call(0x3011)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($tariff_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['tariff_name'] = $x->DataGetString();
            $ret['tariff_create_date'] = $x->DataGetInt();
            $ret['who_create'] = $x->DataGetInt();
            $ret['who_create_login'] = $x->DataGetString();
            $ret['tariff_change_date'] = $x->DataGetInt();
            $ret['who_change'] = $x->DataGetInt();
            $ret['who_change_login'] = $x->DataGetString();
            $ret['tariff_expire_date'] = $x->DataGetInt();
            $ret['tariff_is_blocked'] = $x->DataGetInt();
            $ret['tariff_balance_rollover'] = $x->DataGetInt();
            $ret['services_count'] = $x->DataGetInt();
            for ($i = 0; $i < $ret['services_count']; $i++) {
                $x = $this->connection->urfa_get_data();
                $service['service_id'] = $x->DataGetInt();
                $service['service_type'] = $x->DataGetInt();
                $service['service_name'] = $x->DataGetString();
                $service['comment'] = $x->DataGetString();
                $service['link_by_default'] = $x->DataGetInt();
                $service['is_dynamic'] = $x->DataGetInt();
                $ret['services'][] = $service;
            }
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_get_sys_users_list()
    { //0x4405
        $ret = array();
        if (!$this->connection->urfa_call(0x4405)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        if ($x = $this->connection->urfa_get_data()) {
            $count = $x->DataGetInt();
            $ret['count'] = $count;
            for ($i = 0; $i < $count; $i++) {
                $x = $this->connection->urfa_get_data();
                $user['user_id'] = $x->DataGetInt();
                $user['login'] = $x->DataGetString();
                $user['ip_address'] = $x->DataGetIPAddress();
                $user['mask'] = $x->DataGetIPAddress();
                $ret['users'][] = $user;
            }
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_get_sys_user($user_id)
    { //0x4409
        $ret = array();
        if (!$this->connection->urfa_call(0x4409)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['login'] = $x->DataGetString();
            $ret['ip'] = $x->DataGetIPAddress();
            $ret['mask'] = $x->DataGetIPAddress();
            $ret['group_count'] = $x->DataGetInt();
            for ($i = 0; $i < $ret['group_count']; $i++) {
                $group['group_id'] = $x->DataGetInt();
                $group['group_name'] = $x->DataGetString();
                $ret['groups'][] = $group;
            }
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_add_tariff($tariff_name, $expire_date, $is_blocked,
        $balance_rollover)
    { //0x3012
        if (!$this->connection->urfa_call(0x3012)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetString($tariff_name);
        $packet->DataSetInt($expire_date);
        $packet->DataSetInt($is_blocked);
        $packet->DataSetInt($balance_rollover);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret = $x->DataGetInt();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_edit_tariff($tariff_id, $tariff_name, $expire_date,
        $is_blocked, $balance_rollover)
    { //0x3013
        $ret = 0;
        if (!$this->connection->urfa_call(0x3013)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($tariff_id);
        $packet->DataSetString($tariff_name);
        $packet->DataSetInt($expire_date);
        $packet->DataSetInt($is_blocked);
        $packet->DataSetInt($balance_rollover);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret = $x->DataGetInt();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_remove_tariff($tariff_id)
    { //0x301b
        $ret = 1;
        if (!$this->connection->urfa_call(0x301b)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($tariff_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret = $x->DataGetInt();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_link_user_tariff($user_id, $account_id = 0, $tariff_current,
        $tariff_next = tariff_current, $discount_period_id, $tariff_link_id = 0)
    { //0x3018
        $ret = array();
        if (!$this->connection->urfa_call(0x3018)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $packet->DataSetInt($account_id);
        $packet->DataSetInt($tariff_current);
        $packet->DataSetInt($tariff_next);
        $packet->DataSetInt($discount_period_id);
        $packet->DataSetInt($tariff_link_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['tariff_link_id'] = $x->DataGetInt();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    /*
        function rpcf_add_once_service_to_user($user_id,$account_id,$service_id) { //0x2551
            $ret=array();
            if (!$this->connection->urfa_call(0x2551)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
                    $service_type = 1;
            $return_type = '';
            $tariff_link_id = 0;
            $slink_id = 0;
            $discount_date = time();
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($account_id);
            $packet->DataSetInt($service_id);
            $packet->DataSetInt($service_type);
            $packet->DataSetString($return_type);
            $packet->DataSetInt($tariff_link_id);
            $packet->DataSetInt($slink_id);
            $packet->DataSetInt($discount_date);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
    //	      		$ret['return_type']=$x->DataGetInt();
    //	      		$ret['error_msg']=$x->DataGetString();
                $this->connection->urfa_get_data();
                }
            return $x;
        }
    */
    function rpcf_add_once_service_to_user($user_id, $account_id, $service_id,
        $tplink, $slink_id, $discount_date, $quantity, $invoice_id)
    { //0x2555
        $ret = array();
        if (!$this->connection->urfa_call(0x2555)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($user_id);
        $packet->DataSetInt($account_id);
        $packet->DataSetInt($service_id);
        $packet->DataSetInt($tplink);
        $packet->DataSetInt($slink_id);
        $packet->DataSetInt($discount_date);
        $packet->DataSetDouble($quantity);
        $packet->DataSetInt($invoice_id);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['result'] = $x->DataGetString();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_get_prepaid_units($slink_id)
    { //0x5500
        $ret = array();
        if (!$this->connection->urfa_call(0x5500)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($slink_id);
        $this->connection->urfa_send_param($packet);
        $x = $this->connection->urfa_get_data();
        $ret['bytes_in_mbyte'] = $x->DataGetInt();
        $x = $this->connection->urfa_get_data();
        $ret['pinfo_size'] = $x->DataGetInt();
        for ($i = 0; $i < $ret['pinfo_size']; $i++) {
            $x = $this->connection->urfa_get_data();
            $pinfo['id'] = $x->DataGetInt();
            $pinfo['old'] = $x->DataGetLong();
            $pinfo['cur'] = $x->DataGetLong();
            $ret[] = $pinfo;
        }
        $this->connection->urfa_get_data();
        return $ret;
    }

    //	function rpcf_add_payment_for_account($account_id,$unused=0,$payment,$currency_id=810,$payment_date,$burn_date=0,$payment_method=1,$admin_comment='',$comment='',$payment_ext_number='',$payment_to_invoice=0,$turn_on_inet=1) { //0x3110
    // Количество параметров уменьшено (Kayfolom)
    function rpcf_add_payment_for_account($account_id, $payment,
        $payment_date, $burn_date, $payment_method, $admin_comment = '', $comment = '',
        $payment_ext_number = '')
    { //0x3110
        $ret = array();
        if (!$this->connection->urfa_call(0x3110)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $unused = 0;
        $currency_id = 810;
        $payment_to_invoice = 0;
        $turn_on_inet = 0;
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($account_id);
        $packet->DataSetInt($unused);
        $packet->DataSetDouble($payment);
        $packet->DataSetInt($currency_id);
        $packet->DataSetInt($payment_date);
        $packet->DataSetInt($burn_date);
        $packet->DataSetInt($payment_method);
        $packet->DataSetString($admin_comment);
        $packet->DataSetString($comment);
        $packet->DataSetString($payment_ext_number);
        $packet->DataSetInt($payment_to_invoice);
        $packet->DataSetInt($turn_on_inet);
        $this->connection->urfa_send_param($packet);
        if ($x = $this->connection->urfa_get_data()) {
            $ret['payment_transaction_id'] = $x->DataGetInt();
            $this->connection->urfa_get_data();
        }
        return $ret;
    }

    function rpcf_save_account($account_id, $account, $block_start_date,
        $block_end_date, $discount_period_id)
    { //0x2032
        if (!$this->connection->urfa_call(0x2032)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        if ($block_start_date == -1)
            $block_start_date = now();
        if ($block_end_date == -1)
            $block_end_date = max_time();
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($account_id);
        $packet->DataSetInt($discount_period_id);
        $packet->DataSetDouble($account['credit']);
        $packet->DataSetInt($account['is_blocked']);
        if ($account['is_blocked'] != 0) {
            $packet->DataSetInt($block_start_date);
            $packet->DataSetInt($block_end_date);
        }
        $packet->DataSetInt($account['dealer_account_id']);
        $packet->DataSetDouble($account['vat_rate']);
        $packet->DataSetDouble($account['sale_tax_rate']);
        $packet->DataSetInt($account['int_status']);
        $packet->DataSetInt($account['block_recalc_abon']);
        $packet->DataSetInt($account['block_recalc_prepaid']);
        $packet->DataSetInt($account['unlimited']);
        $this->connection->urfa_send_param($packet);
        $this->connection->urfa_get_data();
    }

    function rpcf_add_discount_period($id, $start, $expire, $periodic_type_t,
        $cd, $di)
    { //0x2603
        $ret = 0;
        if (!$this->connection->urfa_call(0x2603)) {
            print "Error calling function " . __FUNCTION__ . "\n";
            return FALSE;
        }
        $packet = $this->connection->getPacket();
        $packet->DataSetInt($id);
        $packet->DataSetInt($start);
        $packet->DataSetInt($expire);
        $packet->DataSetInt($periodic_type_t);
        $packet->DataSetInt($cd);
        $packet->DataSetInt($di);
        $this->connection->urfa_send_param($packet);
        $this->connection->urfa_get_data();
    }
}

?>