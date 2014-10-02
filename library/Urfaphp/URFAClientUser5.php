<?php

//require_once (dirname(__FILE__) . "/URFAClient.php");

class Urfaphp_URFAClientUser5 extends Urfaphp_URFAClient
{
    /**
     * Возвращает объект URFAClient_Admin используя текущие настройки подключения
     *
     * @return Urfaphp_URFAClientAdmin
     */
    public function getURFAClient_Admin($login, $pass, $ssl = true)
    {
        return new Urfaphp_URFAClientAdmin($login, $pass, $this->address, $this->port, $ssl);
    }


    function rpcf_user5_add_mime_message($subject, $message, $mime, $state)
    { //-0x4034

        if (!$this->connection->urfa_call(-0x4034)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetString($subject);

        $packet->DataSetString($message);

        $packet->DataSetString($mime);

        $packet->DataSetInt($state);

        $this->connection->urfa_send_param($packet);

    }

    function rpcf_user5_add_message($subject, $message)
    { //-0x4015

        if (!$this->connection->urfa_call(-0x4015)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetString($subject);

        $packet->DataSetString($message);

        $this->connection->urfa_send_param($packet);

        $this->connection->urfa_get_data();

    }

    function rpcf_user5_card_payment_new($account_id, $card_id, $secret)
    { //-0x4045

        $ret = 0;

        if (!$this->connection->urfa_call(-0x4045)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($account_id);

        $packet->DataSetInt($card_id);

        $packet->DataSetString($secret);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret = $x->DataGetInt();

            if ($ret == 0) $ret = $x->DataGetString();

        } else {

            return -1;

        }

        return $ret;

    }

    function rpcf_user5_blocks_report($start_date, $end_date)
    { //-0x4013

        $ret = array();

        if (!$this->connection->urfa_call(-0x4013)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($start_date);

        $packet->DataSetInt($end_date);

        $this->connection->urfa_send_param($packet);

        $x = $this->connection->urfa_get_data();

        $ret['count'] = $x->DataGetInt();

        for ($i = 0; $i < $ret['count']; $i++) {

            //			$x = $this->connection->urfa_get_data();

            $block['account_id'] = $x->DataGetInt();

            $block['start_date'] = $x->DataGetInt();

            $block['expire_date'] = $x->DataGetInt();

            $block['what_blocked'] = $x->DataGetInt();

            $block['block_type'] = $x->DataGetInt();

            $block['comment'] = $x->DataGetString();

            $ret['block'][] = $block;

        }

        //		$this->connection->urfa_get_data();

        return $ret;

    }

    function rpcf_user5_card_payment($account_id, $card_id, $secret)
    { //-0x4205

        $ret = 0;

        if (!$this->connection->urfa_call(-0x4205)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($account_id);

        $packet->DataSetInt($card_id);

        $packet->DataSetString($secret);

        $this->connection->urfa_send_param($packet);

        $this->connection->urfa_get_data();

    }

    function rpcf_user5_brief_report_for_wintray()
    { //-0x4026

        $ret = array();

        if (!$this->connection->urfa_call(-0x4026)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $x = $this->connection->urfa_get_data();

        $ret['int_status'] = $x->DataGetInt();

        $ret['balance'] = $x->DataGetDouble();

        //		$this->connection->urfa_get_data();

        return $ret;

    }

    function rpcf_user5_change_int_status($status)
    { //-0x4007

        $ret = array();

        if (!$this->connection->urfa_call(-0x4007)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($status);

        $this->connection->urfa_send_param($packet);

        $this->connection->urfa_get_data();

    }

    function rpcf_user5_change_password($old_password, $new_password, $new_password_ret)
    { //-0x4021

        $ret = array();

        if (!$this->connection->urfa_call(-0x4021)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetString($old_password);

        $packet->DataSetString($new_password);

        $packet->DataSetString($new_password_ret);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['result'] = $x->DataGetInt();

        }

        return $ret;

    }

    function rpcf_user5_change_password_service($slink_id, $item_id, $old_password, $new_password, $new_password_ret)
    { //-0x4025

        $ret = array();

        if (!$this->connection->urfa_call(-0x4025)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($slink_id);

        $packet->DataSetInt($item_id);

        $packet->DataSetString($old_password);

        $packet->DataSetString($new_password);

        $packet->DataSetString($new_password_ret);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['result'] = $x->DataGetInt();

        }

        return $ret;

    }

    function rpcf_user5_dhs_report($start_date, $end_date)
    { //-0x4017

        $ret = array();

        if (!$this->connection->urfa_call(-0x4017)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($start_date);

        $packet->DataSetInt($end_date);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['dhs_log_size'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['dhs_log_size']; $i++) {

                //            $x = $this->connection->urfa_get_data();

                $session['id'] = $x->DataGetInt();

                $session['account_id'] = $x->DataGetInt();

                $session['slink_id'] = $x->DataGetInt();

                $session['recv_date'] = $x->DataGetInt();

                $session['last_update_date'] = $x->DataGetInt();

                $session['framed_ip'] = $x->DataGetInt();

                $session['nas_port'] = $x->DataGetInt();

                $session['acct_session_id'] = $x->DataGetString();

                $session['nas_port_type'] = $x->DataGetInt();

                $session['uname'] = $x->DataGetString();

                $session['service_type'] = $x->DataGetInt();

                $session['framed_protocol'] = $x->DataGetInt();

                $session['nas_ip'] = $x->DataGetInt();

                $session['nas_id'] = $x->DataGetString();

                $session['acct_status_type'] = $x->DataGetInt();

                $session['acct_inp_pack'] = $x->DataGetLong();

                $session['acct_inp_oct'] = $x->DataGetLong();

                $session['acct_out_pack'] = $x->DataGetLong();

                $session['acct_out_oct'] = $x->DataGetLong();

                $session['acct_sess_time'] = $x->DataGetLong();

                $session['dhs_sessions_detail_size'] = $x->DataGetInt();

                for ($j = 0; $j < $session['dhs_sessions_detail_size']; $j++) {

                    $session['dhs_sessions_detail_list'][$j]['trange_id'] = $x->DataGetInt();

                    $session['dhs_sessions_detail_list'][$j]['account_id'] = $x->DataGetInt();

                    $session['dhs_sessions_detail_list'][$j]['duration'] = $x->DataGetLong();

                    $session['dhs_sessions_detail_list'][$j]['base_cost'] = $x->DataGetDouble();

                    $session['dhs_sessions_detail_list'][$j]['sum_cost'] = $x->DataGetDouble();

                }

                $ret['sessions'][] = $session;

            }

            //         $this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_general_report($t_start, $t_end)
    { //-0x4008

        $ret = array();

        if (!$this->connection->urfa_call(-0x4008)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($t_start);

        $packet->DataSetInt($t_end);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['count'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['count']; $i++) {

                $report['account_id'] = $x->DataGetInt();

                $report['incoming_rest'] = $x->DataGetDouble();

                $report['services_discount_1'] = $x->DataGetDouble();

                $report['services_discount_2'] = $x->DataGetDouble();

                $report['services_discount_3'] = $x->DataGetDouble();

                $report['services_discount_5'] = $x->DataGetDouble();

                $report['services_discount_6'] = $x->DataGetDouble();

                $report['payments'] = $x->DataGetDouble();

                $report['outgoing_rest'] = $x->DataGetDouble();

                $ret['report'][] = $report;

            }

        }

        return $ret;

    }

    function rpcf_user5_get_accounts()
    { //-0x4055

        $ret = array();

        if (!$this->connection->urfa_call(-0x4055)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $x = $this->connection->urfa_get_data();

        $ret['accounts_size'] = $x->DataGetInt();

        for ($i = 0; $i < $ret['accounts_size']; $i++) {

            //			$x = $this->connection->urfa_get_data();

            $account['account_id'] = $x->DataGetInt();

            $account['balance'] = $x->DataGetDouble();

            $account['credit'] = $x->DataGetDouble();

            $ret['accounts'][] = $account;

        }

        //		$this->connection->urfa_get_data();

        return $ret;

    }

    function rpcf_user5_get_group_id_by_name($name)
    { //-0x401b

        $ret = array();

        if (!$this->connection->urfa_call(-0x401b)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetString($name);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['group_name'] = $name;

            $ret['group_id'] = $x->DataGetInt();

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_get_remaining_seconds($user_id)
    { //-0x2027

        $ret = array();

        if (!$this->connection->urfa_call(-0x2027)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($user_id);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['remaining_seconds'] = $x->DataGetInt();

            $ret['downloaded_seconds'] = $x->DataGetInt();

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_get_remaining_traffic($user_id)
    { //-0x2026

        $ret = array();

        if (!$this->connection->urfa_call(-0x2026)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($user_id);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['traffic_remaining_mb'] = $x->DataGetDouble();

            $ret['traffic_downloaded_mb'] = $x->DataGetDouble();

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_get_service_id_by_name($name)
    { //-0x401e

        $ret = array();

        if (!$this->connection->urfa_call(-0x401e)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetString($name);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['service_name'] = $name;

            $ret['service_id'] = $x->DataGetInt();

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_get_services()
    { //-0x4023

        $ret = array();

        if (!$this->connection->urfa_call(-0x4023)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $x = $this->connection->urfa_get_data();

        $ret['count'] = $x->DataGetInt();

        for ($i = 0; $i < $ret['count']; $i++) {

            //			$x = $this->connection->urfa_get_data();

            $service['id'] = $x->DataGetInt();

            $service['service_id'] = $x->DataGetInt();

            $service['service_type'] = $x->DataGetInt();

            $service['service_name'] = $x->DataGetString();

            $service['tariff_name'] = $x->DataGetString();

            $service['discount_period'] = $x->DataGetString();

            $service['cost'] = $x->DataGetDouble();

            $service['discounted_in_curr_period'] = $x->DataGetDouble();

            $ret['services'][] = $service;

        }

        //		$this->connection->urfa_get_data();

        return $ret;

    }

    function rpcf_user5_get_services_info($slink_id)
    { //-0x4024

        $ret = array();

        if (!$this->connection->urfa_call(-0x4024)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($slink_id);

        $this->connection->urfa_send_param($packet);

        $x = $this->connection->urfa_get_data();

        $ret['service_type'] = $x->DataGetInt();

        $ret['service_id'] = $x->DataGetInt();

        $ret['service_name'] = $x->DataGetString();

        $ret['tariff_id'] = $x->DataGetInt();

        $ret['discounted_in_curr_period'] = $x->DataGetDouble();

        $ret['cost'] = $x->DataGetDouble();

        //		$x = $this->connection->urfa_get_data();

        switch ($ret['service_type']) {

            case "3":

                $ret['bytes_in_mbyte'] = $x->DataGetInt();

                $ret['iptsl_downloaded_size'] = $x->DataGetInt();

                for ($i = 0; $i <= $ret['iptsl_downloaded_size'] - 1; $i++) {

                    //					$x = $this->connection->urfa_get_data();

                    $ret['iptsl_downloaded_size_list'][$i]['tclass'] = $x->DataGetString();

                    $ret['iptsl_downloaded_size_list'][$i]['downloaded'] = $x->DataGetLong();

                }

//				$x = $this->connection->urfa_get_data();

                $ret['iptsl_old_prepaid_size'] = $x->DataGetInt();

                for ($i = 0; $i <= $ret['iptsl_old_prepaid_size'] - 1; $i++) {

                    //					$x = $this->connection->urfa_get_data();

                    $ret['iptsl_old_prepaid_size_list'][$i]['tclass'] = $x->DataGetString();

                    $ret['iptsl_old_prepaid_size_list'][$i]['downloaded'] = $x->DataGetLong();

                }

//				$x = $this->connection->urfa_get_data();

                $ret['ipgroup_size'] = $x->DataGetInt();

                for ($i = 0; $i <= $ret['ipgroup_size'] - 1; $i++) {

                    //					$x = $this->connection->urfa_get_data();

                    $ret['ipgroup_size_list'][$i]['item_id'] = $x->DataGetInt();

                    $ret['ipgroup_size_list'][$i]['ip'] = $x->DataGetIPAddress();

                    $ret['ipgroup_size_list'][$i]['mask'] = $x->DataGetIPAddress();

                    $ret['ipgroup_size_list'][$i]['login'] = $x->DataGetString();

                }

//				$x = $this->connection->urfa_get_data();

                $ret['iptsd_borders_size'] = $x->DataGetInt();

                for ($i = 0; $i <= $ret['iptsd_borders_size'] - 1; $i++) {

                    //					$x = $this->connection->urfa_get_data();

                    $ret['iptsd_borders_size_list'][$i]['tclass_name'] = $x->DataGetString();

                    $ret['iptsd_borders_size_list'][$i]['bytes'] = $x->DataGetLong();

                    $ret['iptsd_borders_size_list'][$i]['cost1'] = $x->DataGetDouble();

                    $ret['iptsd_borders_size_list'][$i]['group_type'] = $x->DataGetInt();

                }

//				$x = $this->connection->urfa_get_data();

                $ret['iptsd_prepaid_size'] = $x->DataGetInt();

                for ($i = 0; $i <= $ret['iptsd_prepaid_size'] - 1; $i++) {

                    //					$x = $this->connection->urfa_get_data();

                    $ret['iptsd_prepaid_size_list'][$i]['tclass_name_p'] = $x->DataGetString();

                    $ret['iptsd_prepaid_size_list'][$i]['prepaid_p'] = $x->DataGetLong();

                }

                break;

            case "6":

                $ret['tsl_numbers_size'] = $x->DataGetInt();

                for ($i = 0; $i <= $ret['tsl_numbers_size'] - 1; $i++) {

                    //					$x = $this->connection->urfa_get_data();

                    $ret['tsl_numbers_size_list'][$i]['number'] = $x->DataGetString();

                    $ret['tsl_numbers_size_list'][$i]['login'] = $x->DataGetString();

                    $ret['tsl_numbers_size_list'][$i]['allowed_cid'] = $x->DataGetString();

                    $ret['tsl_numbers_size_list'][$i]['item_id'] = $x->DataGetInt();

                }

                break;

            default:

                $ret['null_param'] = $x->DataGetInt();

                break;

        }

        //		$this->connection->urfa_get_data();

        return $ret;
    }

    function rpcf_user5_get_tariff_id_by_name($name)
    { //-0x401a

        $ret = array();

        if (!$this->connection->urfa_call(-0x401a)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetString($name);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['tariff_name'] = $name;

            $ret['tariff_id'] = $x->DataGetInt();

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_get_tariff_name($tariff_id)
    { //-0x4039

        $ret = array();

        if (!$this->connection->urfa_call(-0x4039)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($tariff_id);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['tariff_name'] = $x->DataGetString();

        }

        return $ret;

    }

    function rpcf_user5_get_tel_report($time_start, $time_end)
    { //-0x4099

        if (!$this->connection->urfa_call(-0x4099)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($time_start);

        $packet->DataSetInt($time_end);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $count = $x->DataGetInt();

            $ret['count'] = $count;

            for ($i = 0; $i < $count; $i++) {

                $dhs_log_size = $x->DataGetInt();

                $tel['dhs_log_size'] = $dhs_log_size;

                for ($j = 0; $j < $dhs_log_size; $j++) {

                    $report['recv_date'] = $x->DataGetInt();

                    $report['recv_date_plus_acct_sess_time'] = $x->DataGetInt();

                    $report['acct_sess_time'] = $x->DataGetInt();

                    $report['Calling_Station_Id'] = $x->DataGetString();

                    $report['Called_Station_Id'] = $x->DataGetString();

                    $report['dname'] = $x->DataGetString();

                    $report['total_cost'] = $x->DataGetDouble();

                    $tel['report'][] = $report;

                }

                $ret['tel'][] = $tel;

                unset($tel);

            }

            return $ret;

        }

    }


//Функцию нужно проверить.
    function rpcf_user5_get_user_group_list()
    { //-0x401c

        $ret = array();

        if (!$this->connection->urfa_call(-0x401c)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $x = $this->connection->urfa_get_data();

        $ret['count'] = $x->DataGetInt();

        for ($i = 0; $i < $ret['count']; $i++) {

            $group['id'] = $x->DataGetInt();

            $ret['groups'][] = $group;

        }

        //		$this->connection->urfa_get_data();

        return $ret;

    }

    function rpcf_user5_get_user_info()
    { //-0x4006

        $ret = array();

        if (!$this->connection->urfa_call(-0x4006)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        if ($x = $this->connection->urfa_get_data()) {

            $ret['user_id'] = $x->DataGetInt();

            $ret['login'] = $x->DataGetString();

            $ret['basic_account'] = $x->DataGetInt();

            $ret['balance'] = $x->DataGetDouble();

            $ret['credit'] = $x->DataGetDouble();

            $ret['is_blocked'] = $x->DataGetInt();

            $ret['create_date'] = $x->DataGetInt();

            $ret['last_change_date'] = $x->DataGetInt();

            $ret['who_create'] = $x->DataGetInt();

            $ret['who_change'] = $x->DataGetInt();

            $ret['is_juridical'] = $x->DataGetInt();

            $ret['full_name'] = $x->DataGetString();

            $ret['juridical_address'] = $x->DataGetString();

            $ret['actual_address'] = $x->DataGetString();

            $ret['work_telephone'] = $x->DataGetString();

            $ret['home_telephone'] = $x->DataGetString();

            $ret['mobile_telephone'] = $x->DataGetString();

            $ret['web_page'] = $x->DataGetString();

            $ret['icq_number'] = $x->DataGetString();

            $ret['tax_number'] = $x->DataGetString();

            $ret['kpp_number'] = $x->DataGetString();

            $ret['bank_id'] = $x->DataGetInt();

            $ret['bank_account'] = $x->DataGetString();

            $ret['int_status'] = $x->DataGetInt();

            $ret['vat_rate'] = $x->DataGetDouble();

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_messages_list($time_start, $time_end)
    { //-0x4014

        $ret = array();

        if (!$this->connection->urfa_call(-0x4014)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($time_start);

        $packet->DataSetInt($time_end);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['messages_size'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['messages_size']; $i++) {

                $messages['send_date'] = $x->DataGetInt();

                $messages['recv_date'] = $x->DataGetInt();

                $messages['subject'] = $x->DataGetString();

                $messages['message'] = $x->DataGetString();

                $ret['messages'][] = $messages;

            }

        }

        return $ret;

    }

    function rpcf_user5_messages_list_to_now($time_start)
    { //-0x4028

        $ret = array();

        if (!$this->connection->urfa_call(-0x4028)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($time_start);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['time_end'] = $x->DataGetInt();

            $ret['messages_size'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['messages_size']; $i++) {

                $messages_to_now['send_date'] = $x->DataGetInt();

                $messages_to_now['recv_date'] = $x->DataGetInt();

                $messages_to_now['subject'] = $x->DataGetString();

                $messages_to_now['message'] = $x->DataGetString();

                $ret['messages_to_now'][] = $messages_to_now;

            }

        }

        return $ret;

    }

    function rpcf_user5_mime_messages_list($time_start, $time_end)
    { //-0x4032

        $ret = array();

        if (!$this->connection->urfa_call(-0x4032)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($time_start);

        $packet->DataSetInt($time_end);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['messages_size'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['messages_size']; $i++) {

                $messages['send_date'] = $x->DataGetInt();

                $messages['recv_date'] = $x->DataGetInt();

                $messages['subject'] = $x->DataGetString();

                $messages['message'] = $x->DataGetString();

                $messages['mime'] = $x->DataGetString();

                $messages['state'] = $x->DataGetInt();

                $ret['messages'][] = $messages;

            }

        }

        return $ret;

    }

    function rpcf_user5_mime_messages_list_to_now($time_start)
    { //-0x4033

        $ret = array();

        if (!$this->connection->urfa_call(-0x4033)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($time_start);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['time_end'] = $x->DataGetInt();

            $ret['messages_size'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['messages_size']; $i++) {

                $messages_to_now['send_date'] = $x->DataGetInt();

                $messages_to_now['recv_date'] = $x->DataGetInt();

                $messages_to_now['subject'] = $x->DataGetString();

                $messages_to_now['message'] = $x->DataGetString();

                $messages_to_now['mime'] = $x->DataGetString();

                $messages_to_now['state'] = $x->DataGetInt();

                $ret['messages_to_now'][] = $messages_to_now;

            }

        }

        return $ret;

    }

    function rpcf_user5_payments_report($start_date, $end_date)
    { //-0x4012

        $ret = array();

        if (!$this->connection->urfa_call(-0x4012)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($start_date);

        $packet->DataSetInt($end_date);

        $this->connection->urfa_send_param($packet);

        $x = $this->connection->urfa_get_data();

        $ret['account_id'] = $x->DataGetInt();

        $ret['atr_size'] = $x->DataGetInt();

        for ($i = 0; $i < $ret['atr_size']; $i++) {

            $payment['actual_date'] = $x->DataGetInt();

            $payment['payment_enter_date'] = $x->DataGetInt();

            $payment['payment'] = $x->DataGetDouble();

            $payment['payment_incurrency'] = $x->DataGetDouble();

            $payment['currency_id'] = $x->DataGetInt();

            $payment['payment_method_id'] = $x->DataGetInt();

            $payment['payment_method'] = $x->DataGetString();

            $payment['comment'] = $x->DataGetString();

            $ret['payment'][] = $payment;

        }

        //		$this->connection->urfa_get_data();

        return $ret;

    }

    /**
     * Получение отчётов по сервисам
     * @param $start_date
     * @param $end_date
     * @return array|bool
     */
    function rpcf_user5_service_report($start_date, $end_date)
    { //-0x4011

        $ret = array();

        if (!$this->connection->urfa_call(-0x4011)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($start_date);

        $packet->DataSetInt($end_date);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['aids_size'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['aids_size']; $i++) {

                //				$x = $this->connection->urfa_get_data();

                $services['asr_size'] = $x->DataGetInt();

                for ($j = 0; $j < $services['asr_size']; $j++) {

                    //					$x = $this->connection->urfa_get_data();

                    $services['asr_size_array'][$j]['account_id'] = $x->DataGetInt();

                    $services['asr_size_array'][$j]['discount_date'] = $x->DataGetInt();

                    $services['asr_size_array'][$j]['discount'] = $x->DataGetDouble();

                    $services['asr_size_array'][$j]['discount_with_tax'] = $x->DataGetDouble();

                    $services['asr_size_array'][$j]['service_name'] = $x->DataGetString();

                    $services['asr_size_array'][$j]['service_type'] = $x->DataGetInt();

                    $services['asr_size_array'][$j]['comment'] = $x->DataGetString();

                }

                $ret['services'][] = $services;

            }

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcf_user5_switch_internet_on_disconnect($on)
    { //-0x4030

        if (!$this->connection->urfa_call(-0x4030)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($on);

        $this->connection->urfa_send_param($packet);

        $this->connection->urfa_get_data();

    }

    function rpcf_user5_traffic_report($start_date, $end_date)
    { //-0x4009

        $ret = array();

        if (!$this->connection->urfa_call(-0x4009)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($start_date);

        $packet->DataSetInt($end_date);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['account_id'] = $x->DataGetInt();

            $ret['bytes_in_kbyte'] = $x->DataGetDouble();

            $ret['count'] = $x->DataGetInt();

            for ($i = 0; $i < $ret['count']; $i++) {

                //				$x = $this->connection->urfa_get_data();

                $traf['tclass_id'] = $x->DataGetInt();

                $traf['tclass_name'] = $x->DataGetString();

                $traf['bytes'] = $x->DataGetLong();

                $traf['base_cost'] = $x->DataGetDouble();

                $traf['discount'] = $x->DataGetDouble();

                $traf['discount_with_tax'] = $x->DataGetDouble();

                $ret['traffic'][] = $traf;

            }

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

    function rpcs_user5_get_services_name($service_id)
    { //-0x402b

        $ret = array();

        if (!$this->connection->urfa_call(-0x402b)) {

            print "Error calling function " . __FUNCTION__ . "\n";

            return FALSE;

        }

        $packet = $this->connection->getPacket();

        $packet->DataSetInt($service_id);

        $this->connection->urfa_send_param($packet);

        if ($x = $this->connection->urfa_get_data()) {

            $ret['service_type'] = $x->DataGetInt();

            $ret['service_id'] = $x->DataGetInt();

            $ret['service_name'] = $x->DataGetString();

            $ret['service_comment'] = $x->DataGetString();

            $ret['periodic_cost'] = $x->DataGetDouble();

            //			$this->connection->urfa_get_data();

        }

        return $ret;

    }

}

?>