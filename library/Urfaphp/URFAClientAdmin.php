<?php


    class Urfaphp_URFAClientAdmin extends Urfaphp_URFAClient
    {
        /**
        * Возвращает объект URFAClient_User5 используя текущие настройки подключения
        * 
        * @return Urfaphp_URFAClientUser5
        */
        public function getURFAClient_User5($login, $pass, $ssl = true)
        {
            return new Urfaphp_URFAClientUser5($login, $pass, $this->address, $this->port, $ssl);
        }



        function rpcf_add_account($account,$user_id,$is_basic=1,$account_name='auto create account',$discount_period_id=0) { //0x2031
            if (!$this->connection->urfa_call(0x2031)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            if (!isset($account['int_status']))
                $account['int_status']=1;

            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($is_basic);
            $packet->DataSetInt($account['is_blocked']);
            $packet->DataSetString($account_name);
            $packet->DataSetDouble($account['balance']);
            $packet->DataSetDouble($account['credit']);
            $packet->DataSetInt($discount_period_id);
            $packet->DataSetInt($account['dealer_account_id']);
            $packet->DataSetDouble($account['comission_coefficient']);
            $packet->DataSetDouble($account['default_comission_value']);
            $packet->DataSetInt($account['is_dealer']);
            $packet->DataSetDouble($account['vat_rate']);
            $packet->DataSetDouble($account['sale_tax_rate']);
            $packet->DataSetInt($account['int_status']);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret=$x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_add_ip_slink_ex($service) { //0x2928
            $ret = array(); 
            if (!$this->connection->urfa_call(0x2928)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($service['user_id']); 
            $packet->DataSetInt($service['account_id']); 
            $packet->DataSetInt($service['service_id']); 
            $packet->DataSetInt($service['tariff_link_id']); 
            $packet->DataSetInt($service['discount_period_id']); 
            if($service['start_date'] == 0) $service['start_date'] = now(); 
            $packet->DataSetInt($service['start_date']); 
            if($service['expire_date'] == 0) $service['expire_date'] = max_time(); 
            $packet->DataSetInt($service['expire_date']); 
            $packet->DataSetInt($service['unabon']); 
            $packet->DataSetInt($service['unprepay']); 
            $ip_groups_count = count($service['ip_groups']); 
            $packet->DataSetInt($ip_groups_count); 
            for($i=0;$i<$ip_groups_count;$i++) { 
                $packet->DataSetIPAddress($service['ip_groups'][$i]['ip_address']); 
                $packet->DataSetIPAddress($service['ip_groups'][$i]['mask']); 
                $packet->DataSetString($service['ip_groups'][$i]['mac']); 
                $packet->DataSetString($service['ip_groups'][$i]['iptraffic_login']); 
                $packet->DataSetString($service['ip_groups'][$i]['iptraffic_allowed_cid']); 
                $packet->DataSetString($service['ip_groups'][$i]['iptraffic_password']); 
                $packet->DataSetInt($service['ip_groups'][$i]['ip_not_vpn']); 
                $packet->DataSetInt($service['ip_groups'][$i]['dont_use_fw']); 
                $packet->DataSetInt($service['ip_groups'][$i]['router_id']); 
            } 
            $quotas_count = count($service['quotas']); 
            $packet->DataSetInt($quotas_count); 
            for($i=0;$i<$quotas_count;$i++) { 
                $packet->DataSetInt($service['quotas'][$i]['tclass_id']); 
                $packet->DataSetDouble($service['quotas'][$i]['quota']); 
            } 

            $this->connection->urfa_send_param($packet); 
            if($x = $this->connection->urfa_get_data()) {
                $ret['slink_id']=$x->DataGetInt(); 
            } else { 
                return -1; 
            }
            return $ret; 
        }

        function rpcf_edit_ip_slink_ex($service) { //0x2929
            $ret = array(); 
            if (!$this->connection->urfa_call(0x2929)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($service['slink_id']); 
            if($service['start_date'] == 0) $service['start_date'] = now(); 
            $packet->DataSetInt($service['start_date']); 
            if($service['expire_date'] == 0) $service['expire_date'] = max_time(); 
            $packet->DataSetInt($service['expire_date']); 
            $ip_groups_count = count($service['ip_address']); 
            $packet->DataSetInt($ip_groups_count); 
            for($i=0;$i<$ip_groups_count;$i++) { 
                $packet->DataSetIPAddress($service['ip_groups'][$i]['ip_address']); 
                $packet->DataSetIPAddress($service['ip_groups'][$i]['mask']); 
                $packet->DataSetString($service['ip_groups'][$i]['mac']); 
                $packet->DataSetString($service['ip_groups'][$i]['iptraffic_login']); 
                $packet->DataSetString($service['ip_groups'][$i]['iptraffic_allowed_cid']); 
                $packet->DataSetString($service['ip_groups'][$i]['iptraffic_password']); 
                $packet->DataSetInt($service['ip_groups'][$i]['ip_not_vpn']); 
                $packet->DataSetInt($service['ip_groups'][$i]['dont_use_fw']); 
                $packet->DataSetInt($service['ip_groups'][$i]['router_id']); 
            } 
            $quotas_count = count($service['quotas']); 
            $packet->DataSetInt($quotas_count); 
            for($i=0;$i<$quotas_count;$i++) { 
                $packet->DataSetInt($service['quotas'][$i]['tclass_id']); 
                $packet->DataSetDouble($service['quotas'][$i]['quota']); 
            } 

            $this->connection->urfa_send_param($packet); 
            if($x = $this->connection->urfa_get_data()) {
                $ret['slink_id']=$x->DataGetInt(); 
            } else { 
                return -1; 
            }
            return $ret; 
        }

	function rpcf_add_periodic_slink_ex($service) { //0x2936
            $ret = array();
            if (!$this->connection->urfa_call(0x2936)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }

            if(!isset($service['tariff_link_id'])) $service['tariff_link_id'] = 0;
            if($service['start_date'] == 0) $service['start_date'] = now();
            if($service['expire_date'] == 0) $service['expire_date'] = max_time(); 
            if(!isset($service['cost_coef'])) $service['cost_coef'] = 1;
            if(!isset($service['unabon'])) $service['unabon'] = 0;

            $packet = $this->connection->getPacket();
            $packet->DataSetInt($service['user_id']);
            $packet->DataSetInt($service['account_id']);
            $packet->DataSetInt($service['service_id']);
            $packet->DataSetInt($service['tariff_link_id']);
            $packet->DataSetInt($service['discount_period_id']);
            $packet->DataSetInt($service['start_date']);
            $packet->DataSetInt($service['expire_date']);
	    $packet->DataSetInt($service['policy_id']);
            $packet->DataSetInt($service['unabon']);
            $packet->DataSetDouble($service['cost_coef']);

            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret['slink_id'] = $x->DataGetInt();
            }else{
                $ret['slink_id'] = -1;
            }
            return $ret;
        } 


        function rpcf_edit_periodic_slink_ex($service) { //0x2937
            $ret = array(); 
            if (!$this->connection->urfa_call(0x2937)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($service['slink_id']); 
            if($service['start_date'] == 0) $service['start_date'] = now(); 
            $packet->DataSetInt($service['start_date']); 
            if($service['expire_date'] == 0) $service['expire_date'] = max_time(); 
            $packet->DataSetInt($service['expire_date']); 
            $packet->DataSetInt($service['policy_id']); 
            $packet->DataSetDouble($service['cost_coef']); 

            $this->connection->urfa_send_param($packet); 
            if($x = $this->connection->urfa_get_data()) {
                $ret['slink_id']=$x->DataGetInt(); 
            } else { 
                return -1; 
            }
            return $ret; 
        }


        function rpcf_add_once_slink_ex($service) { //0x2920
            $ret = array(); 
            if (!$this->connection->urfa_call(0x2920)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($service['user_id']); 
            $packet->DataSetInt($service['account_id']); 
            $packet->DataSetInt($service['service_id']); 
            $packet->DataSetInt($service['tariff_link_id']); 
            if($service['discount_date'] == 0) $service['discount_date'] = now(); 
            $packet->DataSetInt($service['discount_date']); 

            $this->connection->urfa_send_param($packet); 
            if($x = $this->connection->urfa_get_data()) {
                $ret['slink_id']=$x->DataGetInt(); 
            } else { 
                return -1; 
            }
            return $ret; 
        }

        function rpcf_edit_once_slink_ex($service) { //0x2921
            $ret = array(); 
            if (!$this->connection->urfa_call(0x2921)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($service['slink_id']); 
            if($service['discount_date'] == 0) $service['discount_date'] = now(); 
            $packet->DataSetInt($service['discount_date']); 

            $this->connection->urfa_send_param($packet); 
            if($x = $this->connection->urfa_get_data()) {
                $ret['slink_id']=$x->DataGetInt(); 
            } else { 
                return -1; 
            }
            return $ret; 
        }

        function rpcf_service_report_new($user_id=0,$account_id=0,$group_id=0,$apid=0,$time_start,$time_end) {  //3021
            $ret=array();
            if (!$this->connection->urfa_call(0x3021)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($account_id);
            $packet->DataSetInt($group_id);
            $packet->DataSetInt($apid);
            $packet->DataSetInt($time_start);
            $packet->DataSetInt($time_end);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret['accounts_count'] = $x->DataGetInt();
                for ($i=0;$i<$ret['accounts_count'];$i++) {
                    $services['atr_size'] = $x->DataGetInt();
                    for($j=0;$j<$services['atr_size'];$j++){
                        $services['atr_size_array'][$j]['account_id'] = $x->DataGetInt();
                        $services['atr_size_array'][$j]['login'] = $x->DataGetString();                
                        $services['atr_size_array'][$j]['full_name'] = $x->DataGetString();                
                        $services['atr_size_array'][$j]['discount_date'] = $x->DataGetInt();
                        $services['atr_size_array'][$j]['discount_period_id'] = $x->DataGetInt();
                        $services['atr_size_array'][$j]['discount'] = $x->DataGetDouble();
                        $services['atr_size_array'][$j]['service_name'] = $x->DataGetString();
                        $services['atr_size_array'][$j]['service_type'] = $x->DataGetInt();
                    }
                    $ret['services'][]=$services;
                }
            }
            return $ret;
        }



        function rpcf_custom_services_report($time_start,$time_end, $account_id=0, $user_id=0) {  //3114
            $ret=array();
            if (!$this->connection->urfa_call(0x3114)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();

            $packet->DataSetInt($time_start);
            $packet->DataSetInt($time_end);

            $packet->DataSetInt($account_id);
            $packet->DataSetInt($user_id);

            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret['count'] = $x->DataGetInt();
                for ($i=0;$i<$ret['count'];$i++) {
                    $ret[$i]['account_id'] = $x->DataGetInt();
                    $ret[$i]['login'] = $x->DataGetString();                
                    $ret[$i]['date'] = $x->DataGetInt();
                    $ret[$i]['mark'] = $x->DataGetString();
                    $ret[$i]['amount'] = $x->DataGetDouble();
                    $ret[$i]['amount_with_tax'] = $x->DataGetDouble();
                    $ret[$i]['service_name'] = $x->DataGetString();
                    $ret[$i]['service_key'] = $x->DataGetString();
                    $ret[$i]['revoked'] = $x->DataGetInt();
                }
            }
            return $ret;
        }



        function rpcf_traffic_report_ex($user_id,$account_id,$time_start,$time_end, $type) { //0x3009 
            $ret=array(); 
            if (!$this->connection->urfa_call(0x3009)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($type); 
            $packet->DataSetInt($user_id); 
            $packet->DataSetInt($account_id); 
            $packet->DataSetInt(0); 
            $packet->DataSetInt(0); 
            $packet->DataSetInt($time_start); 
            $packet->DataSetInt($time_end); 

            $this->connection->urfa_send_param($packet); 
            if ($x = $this->connection->urfa_get_data()) { 
                $ret['bytes_in_kbyte']=$x->DataGetDouble(); 
                $users_count=$x->DataGetInt(); 
                $ret['users_count']=$users_count; 
                $traffic=array(); 
                for( $i=0; $i<$users_count; $i++ ) { 
                    $atr_size=$x->DataGetInt(); 
                    $traffic['atr_size']=$atr_size; 
                    $ips=array(); 
                    for( $j=0; $j<$atr_size; $j++ ) { 
                        $ips['account_id']=$x->DataGetInt(); 
                        $ips['login']=$x->DataGetString(); 
                        $ips['discount_date']=$x->DataGetInt(); 
                        $ips['tclass']=$x->DataGetInt(); 
                        $ips['base_cost']=$x->DataGetDouble(); 
                        $ips['bytes']=$x->DataGetLong(); 
                        $ips['discount']=$x->DataGetDouble(); 
                        //$ips['XZ']=$x->DataGetInt(); 
                        $traffic['ips'][$j]=$ips; 
                    } 
                    $ret['traffic'][$i]=$traffic; 
                } 
            } 

            return $ret; 
        } 

        function rpcf_add_iptraffic_service_link_ipv6($service) { //0x293a

            $ret = array(); 
            if (!$this->connection->urfa_call(0x293a)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($service['user_id']); 
            $packet->DataSetInt($service['account_id']); 
            $packet->DataSetInt($service['service_id']); 
            $packet->DataSetInt($service['tariff_link_id']); 
            $packet->DataSetInt($service['discount_period_id']); 

            if($service['start_date'] == 0) $service['start_date'] = now(); 
            if($service['expire_date'] == 0) $service['expire_date'] = max_time(); 
            $packet->DataSetInt($service['start_date']); 
            $packet->DataSetInt($service['expire_date']); 

            $packet->DataSetInt($service['policy_id']); 
            $packet->DataSetInt($service['unabon']); 
            
            if (!$service['cost_coef']) $service['cost_coef'] = 1;
            $packet->DataSetDouble($service['cost_coef']); 
            
            $packet->DataSetInt($service['unprepay']);           

            $ip_groups_count = count($service['ip_groups']); 
            $packet->DataSetInt($ip_groups_count); 
            for($i=0;$i<$ip_groups_count;$i++) { 
                $packet->DataSetIP46Address($service['ip_groups'][$i]['ip_address']); 
                $packet->DataSetInt($service['ip_groups'][$i]['mask']); 
                $packet->DataSetString($service['ip_groups'][$i]['mac']); 
                $packet->DataSetString($service['ip_groups'][$i]['login']); 
                $packet->DataSetString($service['ip_groups'][$i]['allowed_cid']); 
                $packet->DataSetString($service['ip_groups'][$i]['password']); 
                $packet->DataSetString($service['ip_groups'][$i]['pool_name']); 

                $packet->DataSetInt($service['ip_groups'][$i]['ip_not_vpn']);
                $packet->DataSetInt($service['ip_groups'][$i]['dont_use_fw']);
                $packet->DataSetInt($service['ip_groups'][$i]['router_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['switch_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['port_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['vlan_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['pool_id']); 
            } 
            $quotas_count = count($service['quotas']); 
            $packet->DataSetInt($quotas_count); 
            for($i=0;$i<$quotas_count;$i++) { 
                $packet->DataSetInt($service['quotas'][$i]['tclass_id']); 
                $packet->DataSetDouble($service['quotas'][$i]['quota']); 
            } 

            $this->connection->urfa_send_param($packet); 
            if($x = $this->connection->urfa_get_data()) { 
                $ret['slink_id']=$x->DataGetInt(); 
            } else { 
                return -1; 
            } 
            return $ret; 
        }


        function rpcf_edit_iptraffic_service_link_ipv6($service) { //0x293b
            $ret = array(); 
            if (!$this->connection->urfa_call(0x293b)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            }

            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($service['slink_id']); 

            if($service['start_date'] == 0) $service['start_date'] = now(); 
            if($service['expire_date'] == 0) $service['expire_date'] = max_time(); 
            $packet->DataSetInt($service['start_date']); 
            $packet->DataSetInt($service['expire_date']); 

            $packet->DataSetInt($service['policy_id']); 
            if (!$service['cost_coef']) $service['cost_coef'] = 1;
            $packet->DataSetDouble($service['cost_coef']); 

            $ip_groups_count = count($service['ip_groups']); 

            $packet->DataSetInt($ip_groups_count); 
            for($i=0;$i<$ip_groups_count;$i++) { 
                $packet->DataSetIP46Address($service['ip_groups'][$i]['ip_address']); 
                $packet->DataSetInt($service['ip_groups'][$i]['mask']); 
                $packet->DataSetString($service['ip_groups'][$i]['mac']); 
                $packet->DataSetString($service['ip_groups'][$i]['login']); 
                $packet->DataSetString($service['ip_groups'][$i]['allowed_cid']); 
                $packet->DataSetString($service['ip_groups'][$i]['password']); 
                $packet->DataSetString($service['ip_groups'][$i]['pool_name']); 

                $packet->DataSetInt($service['ip_groups'][$i]['ip_not_vpn']);                
                $packet->DataSetInt($service['ip_groups'][$i]['dont_use_fw']);                
                $packet->DataSetInt($service['ip_groups'][$i]['router_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['switch_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['port_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['vlan_id']); 
                $packet->DataSetInt($service['ip_groups'][$i]['pool_id']); 
            } 
            $quotas_count = count($service['quotas']); 

            $packet->DataSetInt($quotas_count); 

            for($i=0;$i<$quotas_count;$i++) { 
                $packet->DataSetInt($service['quotas'][$i]['tclass_id']); 
                $packet->DataSetDouble($service['quotas'][$i]['quota']); 
            } 

            $this->connection->urfa_send_param($packet); 

            if($x = $this->connection->urfa_get_data()) { 
                $ret['slink_id']=$x->DataGetInt(); 
            } else { 
                return -1; 
            } 
            return $ret; 
        }

        function rpcf_get_iptraffic_service_link_ipv6($slink_id) { //0x271e
            $service = array(); 
            if (!$this->connection->urfa_call(0x271e)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            }

            $packet = $this->connection->getPacket(); 
            
            $packet->DataSetInt($slink_id); 
            $this->connection->urfa_send_param($packet);  

            if ($x = $this->connection->urfa_get_data()) {
        	//var_dump($x);
            
                $service['tariff_link_id'] = $x->DataGetInt();//0
                $service['is_blocked'] = $x->DataGetInt(); //1
                $service['discount_period_id'] = $x->DataGetInt(); //2
                $service['start_date'] = $x->DataGetInt();//3
                $service['expire_date'] = $x->DataGetInt(); //4

                $service['policy_id'] = $x->DataGetInt(); //5
                $service['costt_coef'] = $x->DataGetDouble();//6 
                $service['unabon'] = $x->DataGetInt(); //7
                $service['unprepay'] = $x->DataGetInt();//8
                $service['tariff_id'] = $x->DataGetInt();//9
                $service['parent_id'] = $x->DataGetInt();//10
                $service['bandwidth_in'] = $x->DataGetInt();//11
                $service['bandwidth_out'] = $x->DataGetInt();//12

                $ip_groups_count = $x->DataGetInt(); //13
                $service['ip_groups_count'] = $ip_groups_count;

                for($i=0;$i<$ip_groups_count;$i++) { 
                    $service['ip_groups'][$i]['ip_address'] = $x->DataGetIP46Address(); 
                    $service['ip_groups'][$i]['mask'] = $x->DataGetInt(); 
                    $service['ip_groups'][$i]['mac'] = $x->DataGetString(); 
                    $service['ip_groups'][$i]['login'] = $x->DataGetString(); 
                    $service['ip_groups'][$i]['password'] = $x->DataGetString(); 
                    $service['ip_groups'][$i]['allowed_cid'] = $x->DataGetString(); 
                    $service['ip_groups'][$i]['pool_name'] = $x->DataGetString(); 

                    $service['ip_groups'][$i]['ip_not_vpn'] = $x->DataGetInt(); 
                    $service['ip_groups'][$i]['dont_use_fw'] = $x->DataGetInt();
                    $service['ip_groups'][$i]['is_dynamic'] = $x->DataGetInt(); 
                    $service['ip_groups'][$i]['router_id'] = $x->DataGetInt(); 
                    $service['ip_groups'][$i]['switch_id'] = $x->DataGetInt(); 
                    $service['ip_groups'][$i]['port_id'] = $x->DataGetInt(); 
                    $service['ip_groups'][$i]['vlan_id'] = $x->DataGetInt(); 
                    $service['ip_groups'][$i]['pool_id'] = $x->DataGetInt(); 
                } 

                $quotas_count = $x->DataGetInt(); 
                $service['quotas_count'] = $quotas_count;
                for($i=0;$i<$quotas_count;$i++) { 
                    $service['quotas'][$i]['tclass_id'] = $x->DataGetInt(); 
                    $service['quotas'][$i]['tclass_name'] = $x->DataGetString(); 
                    $service['quotas'][$i]['quota'] = $x->DataGetLong(); 
                } 
            }

            return $service; 
        }



        function rpcf_get_uaparam_list() //0x440b
        {
            $ret=array();
            if (!$this->connection->urfa_call(0x440b)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }

            if ($x = $this->connection->urfa_get_data()) {
                $count=$x->DataGetInt();
                $ret['uparam_size']=$count;
                for($i=0; $i<$count;$i++) {
                    $uaparam['id']=$x->DataGetInt();
                    $uaparam['name']=$x->DataGetString();
                    $uaparam['display_name']=$x->DataGetString();
                    $uaparam['visible']=$x->DataGetInt();
                    $ret['uaparams'][]=$uaparam;
                }
            }
            return $ret;
        }


        function rpcf_add_group_to_user($user_id,$group_id) { //0x2552
            if (!$this->connection->urfa_call(0x2552)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($group_id);
            $this->connection->urfa_send_param($packet);
        }

        function rpcf_add_payment_for_account($account_id,$payment,$payment_date,$burn_date,$payment_method,$admin_comment='',$comment='',$payment_ext_number='') { //0x3110
            $ret=array();
            if (!$this->connection->urfa_call(0x3110)) { 
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $unused=0;
            $currency_id=810;
            $payment_to_invoice=0;
            $turn_on_inet=0;
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
                $ret['payment_transaction_id']=$x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_add_tariff($tariff_name,$expire_date,$is_blocked,$balance_rollover) { //0x3012
            if (!$this->connection->urfa_call(0x3012)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $ret = array();
            $packet = $this->connection->getPacket();
            $packet->DataSetString($tariff_name);
            $packet->DataSetInt($expire_date);
            $packet->DataSetInt($is_blocked);
            $packet->DataSetInt($balance_rollover);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret['tp_id'] = $x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_get_iptraffic_service_link($slink_id) { //0x2702
            $ret = array();
            if (!$this->connection->urfa_call(0x2702)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($slink_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret['tariff_link_id']=$x->DataGetInt();
                $ret['is_blocked']=$x->DataGetInt();
                $ret['discount_period_id']=$x->DataGetInt();
                $ret['start_date']=$x->DataGetInt();
                $ret['expire_date']=$x->DataGetInt();
                $ret['unabon']=$x->DataGetInt();
                $ret['unprepay']=$x->DataGetInt();
                $ret['tariff_id']=$x->DataGetInt();
                $ret['parent_id']=$x->DataGetInt();
                $ret['ip_groups_count']=$x->DataGetInt();
                for($i=0;$i<$ret['ip_groups_count'];$i++) {
                    $ipgroup['ip_address']=$x->DataGetIPAddress();
                    $ipgroup['mask']=$x->DataGetIPAddress();
                    $ipgroup['mac']=$x->DataGetString();
                    $ipgroup['iptraffic_login']=$x->DataGetString();
                    $ipgroup['iptraffic_password']=$x->DataGetString();
                    $ipgroup['iptraffic_allowed_cid']=$x->DataGetString();
                    $ipgroup['ip_not_vpn']=$x->DataGetInt();
                    $ipgroup['dont_use_fw']=$x->DataGetInt();
                    $ipgroup['router_id']=$x->DataGetInt();
                    $ret['ip_groups'][]=$ipgroup;
                }
                $ret['quotas_count']=$x->DataGetInt();
                for($i=0;$i<$ret['quotas_count'];$i++) {
                    $quota['tclass_id']=$x->DataGetInt();
                    $quota['tclass_name']=$x->DataGetString();
                    $quota['quota']=$x->DataGetLong();
                    $ret['quotas'][]=$quota;
                }
            } else {
                return -1; // invalid slink_id
            }
            return $ret;
        }

        function rpcf_add_user($user,$parameters) { //0x2005
            $ret=array();
            if (!$this->connection->urfa_call(0x2005)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user['user_id']);
            $packet->DataSetString($user['login']);
            $packet->DataSetString($user['password']);
            $packet->DataSetstring($user['full_name']);
            if ($user['user_id'] == 0){
                $unused = 0;
                $packet->DataSetInt($unused);
            }
            $packet->DataSetInt($user['is_juridical']);
            $packet->DataSetString($user['jur_address']);
            $packet->DataSetString($user['act_address']);
            $packet->DataSetString($user['flat_number']);
            $packet->DataSetString($user['entrance']);
            $packet->DataSetString($user['floor']);
            $packet->DataSetString($user['district']);
            $packet->DataSetString($user['building']);
            $packet->DataSetString($user['passport']);
            $packet->DataSetInt($user['house_id']);
            $packet->DataSetString($user['work_tel']);
            $packet->DataSetString($user['home_tel']);
            $packet->DataSetString($user['mob_tel']);
            $packet->DataSetString($user['web_page']);
            $packet->DataSetString($user['icq_number']);
            $packet->DataSetString($user['tax_number']);
            $packet->DataSetString($user['kpp_number']);
            $packet->DataSetString($user['email']);
            $packet->DataSetInt($user['bank_id']);
            $packet->DataSetString($user['bank_account']);
            $packet->DataSetString($user['comments']);
            $packet->DataSetString($user['personal_manager']);
            $packet->DataSetInt($user['connect_date']);
            $packet->DataSetInt($user['is_send_invoice']);
            $packet->DataSetInt($user['advance_payment']);
            $packet->DataSetInt(count($parameters));
            foreach ($parameters as $array_item){
                $packet->DataSetInt($array_item['id']);
                $packet->DataSetString($array_item['value']);
            }
            $this->connection->urfa_send_param($packet);
            $ret['user_id']=0;
            if($x = $this->connection->urfa_get_data()){
                $z_user_id = $x->DataGetInt();
                $error_msg = $x->DataGetString();
                $ret['user_id']=$z_user_id;
                $ret['error_msg']=$error_msg;
            }
            return $ret;
        }

        function rpcf_add_user_new($user) { //0x2125
            $ret=array();
            if (!$this->connection->urfa_call(0x2125)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetString($user['login']);
            $packet->DataSetString($user['password']);
            $packet->DataSetstring($user['full_name']);
            $packet->DataSetInt($user['is_juridical']);
            $packet->DataSetString($user['jur_address']);
            $packet->DataSetString($user['act_address']);
            $packet->DataSetString($user['flat_number']);
            $packet->DataSetString($user['entrance']);
            $packet->DataSetString($user['floor']);
            $packet->DataSetString($user['district']);
            $packet->DataSetString($user['building']);
            $packet->DataSetString($user['passport']);
            $packet->DataSetInt($user['house_id']);
            $packet->DataSetString($user['work_tel']);
            $packet->DataSetString($user['home_tel']);
            $packet->DataSetString($user['mob_tel']);
            $packet->DataSetString($user['web_page']);
            $packet->DataSetString($user['icq_number']);
            $packet->DataSetString($user['tax_number']);
            $packet->DataSetString($user['kpp_number']);
            $packet->DataSetString($user['email']);
            $packet->DataSetInt($user['bank_id']);
            $packet->DataSetString($user['bank_account']);
            $packet->DataSetString($user['comments']);
            $packet->DataSetString($user['personal_manager']);
            $packet->DataSetInt($user['connect_date']);
            $packet->DataSetInt($user['is_send_invoice']);
            $packet->DataSetInt($user['advance_payment']);

            $packet->DataSetInt($user['switch_id']);
            $packet->DataSetInt($user['port_number']);
            if(!$user['binded_currency_id']) $user['binded_currency_id'] = 810;
            $packet->DataSetInt($user['binded_currency_id']);

            if(!is_array($user['groups'])) $user['groups'] = array();
            if(!is_array($user['parameters'])) $user['parameters'] = array();

            $packet->DataSetInt(count($user['parameters']));
            foreach ($user['parameters'] as $array_item){
                $packet->DataSetInt($array_item['id']);
                $packet->DataSetString($array_item['value']);
            }

            $packet->DataSetInt(count($user['groups']));
            foreach ($user['groups'] as $array_item){
                $packet->DataSetInt($array_item);
            }

            $packet->DataSetInt($user['is_blocked']);
            $packet->DataSetDouble($user['balance']);
            $packet->DataSetDouble($user['credit']);
            $packet->DataSetDouble($user['vat_rate']);
            $packet->DataSetDouble($user['sale_tax_rate']);
            $packet->DataSetInt($user['int_status']);

            $this->connection->urfa_send_param($packet);
            $ret['user_id']=0;
            if($x = $this->connection->urfa_get_data()){
                $z_user_id = $x->DataGetInt();
                if($z_user_id == 0)
                {
                    $error_msg = $x->DataGetString();
                    $ret['error_msg']=$error_msg;
                }
                $ret['user_id']=$z_user_id;
                $ret['basic_account']=$x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_edit_user_new($user,$parameters) { //0x2126
            $ret = array();
            if (!$this->connection->urfa_call(0x2126)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user['user_id']);
            $packet->DataSetString($user['login']);
            $packet->DataSetString($user['password']);
            $packet->DataSetString($user['full_name']);
            $packet->DataSetInt($user['is_juridical']);
            $packet->DataSetString($user['jur_address']);
            $packet->DataSetString($user['act_address']);
            $packet->DataSetString($user['flat_number']);
            $packet->DataSetString($user['entrance']);
            $packet->DataSetString($user['floor']);
            $packet->DataSetString($user['district']);
            $packet->DataSetString($user['building']);
            $packet->DataSetString($user['passport']);
            $packet->DataSetInt($user['house_id']);
            $packet->DataSetString($user['work_tel']);
            $packet->DataSetString($user['home_tel']);
            $packet->DataSetString($user['mob_tel']);
            $packet->DataSetString($user['web_page']);
            $packet->DataSetString($user['icq_number']);
            $packet->DataSetString($user['tax_number']);
            $packet->DataSetString($user['kpp_number']);
            $packet->DataSetString($user['email']);
            $packet->DataSetInt($user['bank_id']);
            $packet->DataSetString($user['bank_account']);
            $packet->DataSetString($user['comments']);
            $packet->DataSetString($user['personal_manager']);
            $packet->DataSetInt($user['connect_date']);
            $packet->DataSetInt($user['is_send_invoice']);
            $packet->DataSetInt($user['advance_payment']);
            @$packet->DataSetInt($user['switch_id']);
            @$packet->DataSetInt($user['port_number']);
            @$packet->DataSetInt($user['binded_currency_id']);
            $packet->DataSetInt(count($parameters));
            foreach ($parameters as $array_item){
                $packet->DataSetInt($array_item['id']);
                $packet->DataSetString($array_item['value']);
            }
            $this->connection->urfa_send_param($packet);
            $ret['user_id']=0;
            if($x = $this->connection->urfa_get_data()){
                $ret['user_id'] = $x->DataGetInt();
                    if(!'user_id') {
                    $ret['error_msg'] = $x->DataGetString();
                }
            }
            return $ret;
        }


        function rpcf_core_build() { //0x0046
            $ret=array();
            if (!$this->connection->urfa_call(0x0046)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $ret['core_build']=$x->DataGetString();
            return $ret;
        }
        function rpcf_block_account($account_id, $block) { //0x2037
            $ret=array();
            if (!$this->connection->urfa_call(0x2037)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($account_id);
            $packet->DataSetInt($block);
            $this->connection->urfa_send_param($packet);
        }
        function rpcf_change_intstat_for_user($user_id,$block) { //0x2003
            if (!$this->connection->urfa_call(0x2003)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($block);
            $this->connection->urfa_send_param($packet);
        }

        function rpcf_get_periodic_component_of_cost($sid) { //0x10000
            if (!$this->connection->urfa_call(0x10000)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($sid);
            $this->connection->urfa_send_param($packet);
            $x = $this->connection->urfa_get_data();
            $ret['cost']=$x->DataGetDouble();
        }

        function rpcf_core_version() { //0x0045
            $ret=array();
            if (!$this->connection->urfa_call(0x0045)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $ret['core_version']=$x->DataGetString();
            return $ret;
        }

        function rpcf_edit_tariff($tariff_id,$tariff_name,$expire_date,$is_blocked,$balance_rollover) { //0x3013
            $ret=0;
            if (!$this->connection->urfa_call(0x3013)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($tariff_id);
            $packet->DataSetString($tariff_name);
            $packet->DataSetInt($expire_date);
            $packet->DataSetInt($is_blocked);
            $packet->DataSetInt($balance_rollover);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret = $x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_general_report_new($user_id=0,$account_id=0,$group_id=0,$discount_period_id=0,$start_date,$end_date) { //0x3022
            $ret=array();
            if (!$this->connection->urfa_call(0x3022)) {
                print "Error calling function ". __FUNCTION__ ."\n";
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
            $count=$x->DataGetInt();
            $ret['count']=$count;
            for ($i=0;$i<$count;$i++) {
                $rep['account_id']=$x->DataGetInt();
                $rep['login']=$x->DataGetString();
                $rep['full_name']=$x->DataGetString();
                $rep['incoming_rest']=$x->DataGetDouble();
                $rep['discounted_once']=$x->DataGetDouble();
                $rep['discounted_periodic']=$x->DataGetDouble();
                $rep['discounted_iptraffic']=$x->DataGetDouble();
                $rep['discounted_hotspot']=$x->DataGetDouble();
                $rep['discounted_dialup']=$x->DataGetDouble();
                $rep['discounted_telephony']=$x->DataGetDouble();
                $rep['discounted_other_charges']=$x->DataGetDouble();
                $rep['tax']=$x->DataGetDouble();
                $rep['discounted_with_tax']=$x->DataGetDouble();
                $rep['payments']=$x->DataGetDouble();
                $rep['outgoing_rest']=$x->DataGetDouble();
                $ret['report'][]=$rep;
            }
            return $ret;
        }

        function rpcf_get_all_services_for_user($account_id) { //0x2700
            $ret=array();
            if (!$this->connection->urfa_call(0x2700)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($account_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $count=$x->DataGetInt();
                $ret['count']=$count;
                for($i=0; $i<$count;$i++) {
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
                    $ret['services'][]=$service;
                }
            }
            return $ret;
        }

        function rpcf_get_bytes_in_kb() { //0x10002
            $ret=array();
            if (!$this->connection->urfa_call(0x10002)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $ret['bytes_in_kb']=$x->DataGetInt();
            return $ret;
        }
        function rpcf_get_currency_list() { //0x2910
            $ret=array();
            if (!$this->connection->urfa_call(0x2910)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $count = $x->DataGetInt();
            $ret['count']= $count;
            for ($i=0;$i<$count;$i++) {
                $currency['id'] = $x->DataGetInt();
                $currency['currency_brief_name'] = $x->DataGetString();
                $currency['currency_full_name'] = $x->DataGetString();
                $currency['percent'] = $x->DataGetDouble();
                $currency['rates'] = $x->DataGetDouble();
                $ret['currency'][]=$currency;
            }
            return $ret;
        }
        function rpcf_get_discount_period($period_id) { //0x2609
            $ret=array();
            if (!$this->connection->urfa_call(0x2609)) {
                print "Error calling function ". __FUNCTION__ ."\n";
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
                $ret['invoice_month'] = $x->DataGetInt();
            }
            return $ret;
        }
        function rpcf_get_discount_periods() { //0x2600
            $ret=array();
            if (!$this->connection->urfa_call(0x2600)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();//Periods count
            $count = $x->DataGetInt();
            $ret['count']= $count;
            for ($i=0;$i<$count;$i++) {
                $period['static_id']=$x->DataGetInt();
                $period['discount_period_id']=$x->DataGetInt();
                $period['start_date']=$x->DataGetInt();
                $period['end_date']=$x->DataGetInt();
                $period['periodic_type']=$x->DataGetInt();
                $period['custom_duration']=$x->DataGetInt();
                $period['next_discount_period_id']=$x->DataGetInt();
                $period['canonical_length']=$x->DataGetInt();
                $ret['discount_periods'][]=$period;
            }
            return $ret;
        }

        function rpcf_get_groups_for_user($user_id) { //0x2550
            $ret=array();
            if (!$this->connection->urfa_call(0x2550)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $this->connection->urfa_send_param($packet);
            $x = $this->connection->urfa_get_data();
            $count=$x->DataGetInt();
            $ret['count']=$count;
            for ($i=0;$i<$count;$i++) {
                $group['group_id']=$x->DataGetInt();
                $group['group_name']=$x->DataGetString();
                $ret['group'][]=$group;
            }
            return $ret;
        }
        function rpcf_get_groups_list($user_id=0) { //0x2400
            $ret=array();
            if (!$this->connection->urfa_call(0x2400)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $this->connection->urfa_send_param($packet);
            $x = $this->connection->urfa_get_data();
            $count=$x->DataGetInt();
            $ret['count']=$count;
            for ($i=0;$i<$count;$i++) {
                $group['group_id']=$x->DataGetInt();
                $group['group_name']=$x->DataGetString();
                $ret['group'][]=$group;
            }
            return $ret;
        }
        function rpcf_get_ipgroup($ipgroup_id) { //0x2902
            $ret=array();
            if (!$this->connection->urfa_call(0x2902)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($ipgroup_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret['name']=$x->DataGetString();
                $ret['count']=$x->DataGetInt();
                for ($i=0;$i<$ret['count'];$i++) {
                    $set['ip']=$x->DataGetIPAddress();
                    $set['mask']=$x->DataGetIPAddress();
                    $set['gateway']=$x->DataGetIPAddress();
                    $ret['ipgroup'][]=$set;
                }
            }
            return $ret;
        }

        function rpcf_get_ipgroups_list_ipv6() { //0x292e
            $ret=array();
            if (!$this->connection->urfa_call(0x292e)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $groups_count=$x->DataGetInt();
            $ret['groups_count']=$groups_count;
            for ($i=0;$i<$groups_count;$i++) {
                $count=$x->DataGetInt();
                for($j=0; $j<$count;$j++) {
                    $x = $this->connection->urfa_get_data();
                    $group['id']=$x->DataGetInt();
                    $group['ip']=$x->DataGetIP46Address();
                    $group['mask']=$x->DataGetInt();
                    $group['mac']=$x->DataGetString();
                    $group['login']=$x->DataGetString();
                    $group['allowed_cid']=$x->DataGetString();
                    $groups['group'][]=$group;
                }
                $groups['group_count']=$count;
                $ret['groups'][]=$groups;
                unset($groups);
            }
            return $ret;
        }


	function rpcf_get_ippools_list() { //0x1067
            $ret=array();
            if (!$this->connection->urfa_call(0x1067)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $count = $x->DataGetInt();
            $ret['ippools_count'] = $count;
            for ($i=0;$i<$count;$i++) {
                $pool['id']=$x->DataGetInt();
                $pool['name']=$x->DataGetString();
                $pool['address']=$x->DataGetIP46Address();
                $pool['mask']=$x->DataGetInt();
                $ret['ippools'][]=$pool;
            }
            return $ret;
        } 


        function rpcf_get_new_secret($len=8) { //0x0060
            $ret=array();
            if (!$this->connection->urfa_call(0x0060)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($len);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret['error'] = $x->DataGetString();
                $ret['secret'] = $x->DataGetString();
            }
            // 0 Error
            return $ret;
        }
        function rpcf_get_payment_methods_list() { //0x3100
            $ret=array();
            if (!$this->connection->urfa_call(0x3100)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $count=$x->DataGetInt();
            $ret['count']=$count;
            for ($i=0; $i < $count; $i++ ) {
                $list['id']=$x->DataGetInt();
                $list['name']=$x->DataGetString();
                $ret['payments_methods'][]=$list;
            }
            return $ret;
        }

        function rpcf_get_prepaid_units($slink_id) { //0x5500
            $ret=array(); 
            if (!$this->connection->urfa_call(0x5500)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($slink_id); 
            $this->connection->urfa_send_param($packet); 
            if($x = $this->connection->urfa_get_data())
            { 
                $ret['bytes_in_mbyte'] = $x->DataGetInt(); 
                $ret['pinfo_size'] = $x->DataGetInt(); 
                for($i=0;$i<$ret['pinfo_size'];$i++) 
                { 
                    //                $x = $this->connection->urfa_get_data(); 
                    $pinfo['id'] = $x->DataGetInt(); 
                    $pinfo['old'] = $x->DataGetLong(); 
                    $pinfo['cur'] = $x->DataGetLong(); 
                    $ret[]=$pinfo; 
                };
                //            $this->connection->urfa_get_data(); 
            }
            return $ret; 
        }


        // Данную функцию необходимо перепроверить
        function rpcf_get_services_list($which_service=-1) { //0x2101
            $ret=array();
            if (!$this->connection->urfa_call(0x2101)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($which_service);
            $this->connection->urfa_send_param($packet);
            if($x = $this->connection->urfa_get_data()){
                $count=$x->DataGetInt();
                $ret['count']=$count;
                for ($i=0;$i<$count;$i++) {
                    $services['service_id']=$x->DataGetInt();
                    $services['service_name']=$x->DataGetString();
                    $services['service_type']=$x->DataGetInt();
                    $services['service_comment']=$x->DataGetString();
                    $service_status=$x->DataGetInt();
                    $services['service_status']=$service_status;
                    if ($service_status==2){
                        $services['tariff_name']=$x->DataGetString();
                    } else {
                        $services['tariff_name']='';
                    }
                    $ret['services'][]=$services;
                }
            }
            return $ret;
        }

        function rpcf_get_sys_user($user_id) { //0x4414
            $ret=array();
            if (!$this->connection->urfa_call(0x4414)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret['login']=$x->DataGetString();
                $ret['ip4']=$x->DataGetIP46Address();
                $ret['mask4']=$x->DataGetInt();
                $ret['ip6']=$x->DataGetIP46Address();
                $ret['mask6']=$x->DataGetInt();

                $ret['groups_count']=$x->DataGetInt();
                for ($i=0;$i<$ret['group_count'];$i++) {
                    $group['group_id']=$x->DataGetInt();
                    $group['group_name']=$x->DataGetString();
                    $ret['groups'][]=$group;
                }
            }
            return $ret;
        }

        function rpcf_get_sys_users_list() { //0x4405
            $ret=array();
            if (!$this->connection->urfa_call(0x4405)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            if ($x = $this->connection->urfa_get_data()) {
                $count=$x->DataGetInt();
                $ret['count']=$count;
                for($i=0;$i<$count;$i++) {
                    $user['user_id']=$x->DataGetInt();
                    $user['login']=$x->DataGetString();
                    $user['ip_address']=$x->DataGetIPAddress();
                    $user['mask']=$x->DataGetIPAddress();
                    $ret['users'][]=$user;
                }
            }
            return $ret;
        }


        function rpcf_get_tariff($tariff_id) { //0x3011
            $ret=array();
            if (!$this->connection->urfa_call(0x3011)) {
                print "Error calling function ". __FUNCTION__ ."\n";
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
                for ($i=0;$i<$ret['services_count'];$i++) {
                    $service['service_id'] = $x->DataGetInt();
                    $service['service_type'] = $x->DataGetInt();
                    $service['service_name'] = $x->DataGetString();
                    $service['comment'] = $x->DataGetString();
                    $service['link_by_default'] = $x->DataGetInt();
                    $service['is_dynamic'] = $x->DataGetInt();
                    $ret['services'][]=$service;
                }
            }
            return $ret;
        }

        function rpcf_get_tariff_id_by_name($name) { //0x301d
            $ret=array();
            if (!$this->connection->urfa_call(0x301d)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket(); 
            $packet->DataSetString($name); 
            $this->connection->urfa_send_param($packet); 

            if($x = $this->connection->urfa_get_data()) {
                $ret['tid']=$x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_get_tariffs_list() { //0x3024
            $ret=array();
            if (!$this->connection->urfa_call(0x3024)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();// Tariff count
            $count = $x->DataGetInt();
            $ret['tariffs_count'] = $count;
            for ($i=0;$i<$count;$i++) {
                $tariff['id']=$x->DataGetInt();
                $tariff['name']=$x->DataGetString();
                $tariff['create_date']=$x->DataGetInt();
                $tariff['who_create']=$x->DataGetInt();
                $tariff['login']=$x->DataGetString();
                $tariff['change_create']=$x->DataGetInt();
                $tariff['who_change']=$x->DataGetInt();
                $tariff['login_change']=$x->DataGetString();
                $tariff['expire_date']=$x->DataGetInt();
                $tariff['is_blocked']=$x->DataGetInt();
                $tariff['balance_rollover']=$x->DataGetInt();
                $tariff['comment']=$x->DataGetString();
                $ret['tariffs'][]=$tariff;
            }
            return $ret;
        }

        function rpcf_get_tclass($class_id) { //0x2306
            $ret=array();
            if (!$this->connection->urfa_call(0x2302)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($class_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret['tclass_name']=$x->DataGetString();
                $ret['graph_color']=$x->DataGetInt();
                $ret['is_display']=$x->DataGetInt();
                $ret['is_fill']=$x->DataGetInt();
                $ret['time_range_id']=$x->DataGetInt();
                $ret['dont_save']=$x->DataGetInt();
                $ret['local_traf_policy']=$x->DataGetInt();
                $ret['tclass_count']=$x->DataGetInt();
                $count = $ret['tclass_count'];
                for ($i=0;$i<$count;$i++) {
                    $x = $this->connection->urfa_get_data();
                    $tclass['saddr']=$x->DataGetIP46Address();
                    $tclass['saddr_mask']=$x->DataGetInt();
                    $tclass['sport']=$x->DataGetInt();
                    $tclass['input']=$x->DataGetInt();
                    $tclass['src_as']=$x->DataGetInt();
                    $tclass['daddr']=$x->DataGetIP46Address();
                    $tclass['daddr_mask']=$x->DataGetInt();
                    $tclass['dport']=$x->DataGetInt();
                    $tclass['output']=$x->DataGetInt();
                    $tclass['dst_as']=$x->DataGetInt();
                    $tclass['proto']=$x->DataGetInt();
                    $tclass['tos']=$x->DataGetInt();
                    $tclass['nexthop']=$x->DataGetInt();
                    $tclass['tcp_flags']=$x->DataGetInt();
                    $tclass['ip_from']=$x->DataGetIP46Address();
                    $tclass['use_sport']=$x->DataGetInt();
                    $tclass['use_input']=$x->DataGetInt();
                    $tclass['use_src_as']=$x->DataGetInt();
                    $tclass['use_dport']=$x->DataGetInt();
                    $tclass['use_output']=$x->DataGetInt();
                    $tclass['use_dst_as']=$x->DataGetInt();
                    $tclass['use_proto']=$x->DataGetInt();
                    $tclass['use_tos']=$x->DataGetInt();
                    $tclass['use_nexthop']=$x->DataGetInt();
                    $tclass['use_tcp_flags']=$x->DataGetInt();
                    $tclass['skip']=$x->DataGetInt();
                    $ret['tclass'][]=$tclass;
                }
            }
            return $ret;
        }
        function rpcf_get_tclasses() { //0x2300
            $ret=array();
            if (!$this->connection->urfa_call(0x2300)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $count=$x->DataGetInt();
            $ret['count']=$count;
            for($i=0; $i<$count;$i++) {
                $x = $this->connection->urfa_get_data();
                $tclass['id']=$x->DataGetInt();
                $tclass['name']=$x->DataGetString();
                $tclass['graph_color']=$x->DataGetInt();
                $tclass['is_display']=$x->DataGetInt();
                $tclass['is_fill']=$x->DataGetInt();
                $ret['tclasses'][]=$tclass;
            }
            return $ret;
        }

        function rpcf_get_user_account_list($user_id) { //0x2033
            $ret=array();
            if (!$this->connection->urfa_call(0x2033)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $count=$x->DataGetInt();
                $ret['count']=$count;
                for($i=0; $i<$count;$i++) {
                    $account['id']=$x->DataGetInt();
                    $account['name']=$x->DataGetString();
                    $ret['accounts'][]=$account;
                }
            }
            return $ret;
        }

        // return user_id or 0 if user not found
        function rpcf_get_user_by_account($account_id) { //0x2026
            $ret=array();
            if (!$this->connection->urfa_call(0x2026)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($account_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $user_id = $x->DataGetInt();
            }
            return $user_id;
        }

        function rpcf_get_user_tariffs($user_id, $account_id=0) { //0x3017
            $ret=array();
            if (!$this->connection->urfa_call(0x3017)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($account_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $count=$x->DataGetInt();
                $ret['count']=$count;
                for($i=0; $i<$count;$i++) {
                    $tariff['current_tariff'] = $x->DataGetInt();
                    $tariff['next_tariff'] = $x->DataGetInt();
                    $tariff['discount_period_id'] = $x->DataGetInt();
                    $tariff['tariff_link_id'] = $x->DataGetInt();
                    $ret['user_tariffs'][]=$tariff;
                }
            }
            return $ret;
        }

        function rpcf_get_userinfo($user_id) { //0x2006
            $ret=array();
            if (!$this->connection->urfa_call(0x2006)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $this->connection->urfa_send_param($packet);
            $x = $this->connection->urfa_get_data();
            $user = $x->DataGetInt();
            $ret['user_id']= $user;
            if ($user!=0) {
                $ret['user_id']= $user;
                $accounts_count = $x->DataGetInt();
                $ret['accounts_count']= $accounts_count;
                
                for($i=0;$i<$accounts_count;$i++) {
                    $accounts['id']=$x->DataGetInt();
                    $accounts['name']=$x->DataGetString();
                    $ret['accounts'][]=$accounts;
                }
                $ret['login']=$x->DataGetString();
                $ret['password']=$x->DataGetString();
                $ret['basic_account']=$x->DataGetInt();
                $ret['full_name']=$x->DataGetString();
                $ret['create_date']=$x->DataGetInt();
                $ret['last_change_date']=$x->DataGetInt();
                $ret['who_create']=$x->DataGetInt();
                $ret['who_change']=$x->DataGetInt();
                $ret['is_juridical']=$x->DataGetInt();
                $ret['jur_address']=$x->DataGetString();
                $ret['act_address']=$x->DataGetString();
                $ret['work_tel']=$x->DataGetString();
                $ret['home_tel']=$x->DataGetString();
                $ret['mob_tel']=$x->DataGetString();
                $ret['web_page']=$x->DataGetString();
                $ret['icq_number']=$x->DataGetString();
                $ret['tax_number']=$x->DataGetString();
                $ret['kpp_number']=$x->DataGetString();
                $ret['bank_id']=$x->DataGetInt();
                $ret['bank_account']=$x->DataGetString();
                $ret['comments']=$x->DataGetString();
                $ret['personal_manager']=$x->DataGetString();
                $ret['connect_date']=$x->DataGetInt();
                $ret['email']=$x->DataGetString();
                $ret['is_send_invoice']=$x->DataGetInt();
                $ret['advance_payment']=$x->DataGetInt();
                $ret['house_id']=$x->DataGetInt();
                $ret['flat_number']=$x->DataGetString();
                $ret['entrance']=$x->DataGetString();
                $ret['floor']=$x->DataGetString();
                $ret['district']=$x->DataGetString();
                $ret['building']=$x->DataGetString();
                $ret['passport']=$x->DataGetString();
                $ret['parameters_size']=$x->DataGetInt();
                for ($i=0; $i < $ret['parameters_size']; $i++ ) {
                    $parameters['id']=$x->DataGetInt();
                    $parameters['value']=$x->DataGetString();
                    $ret['parameters'][]=$parameters;
                }
            }
            return $ret;
        }

        function rpcf_get_users_count($card_user=0) { //0x2011
            $ret=0;
            if (!$this->connection->urfa_call(0x2011)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($card_user);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret = $x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_get_users_list($from,$to,$card_user=0) { //0x2044
            $ret=array();
            if (!$this->connection->urfa_call(0x2044)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($from);
            $packet->DataSetInt($to);
            $packet->DataSetInt($card_user);
            $this->connection->urfa_send_param($packet);
            if($x = $this->connection->urfa_get_data()){
                $count=$x->DataGetInt();
                $ret['count']=$count;
                for ($i=0;$i<$count;$i++) {
                    $users['user_id']=$x->DataGetInt();
                    $users['login']=$x->DataGetString();
                    $users['basic_account']=$x->DataGetInt();
                    $users['full_name']=$x->DataGetString();
                    $users['is_blocked']=$x->DataGetInt();
                    $users['balance']=$x->DataGetDouble();
                    $ip_adr_size=$x->DataGetInt();
                    $users['ip_adr_size']=$ip_adr_size;
                    $ipgroup=array();
                    for ($j=0;$j<$ip_adr_size;$j++) {
                        $group_size=$x->DataGetInt();
                        $ipgroup['group_size']=$group_size;
                        $ips=array();
                        for ($k=0;$k<$group_size;$k++) {
                            $ips['ip_address']=$x->DataGetIP46Address();
                            $ips['mask']=$x->DataGetInt();
                            $ips['group_type']=$x->DataGetInt();
                            $ipgroup['ips'][]=$ips;
                        }
                        $users['ipgroup']=$ipgroup;
                    }
                    $users['user_int_status']=$x->DataGetInt();
                    $ret['users'][]=$users;
                }
            }
            return $ret;
        }

        function rpcf_link_user_tariff($user_id,$account_id=0,$tariff_current,$tariff_next=tariff_current,$discount_period_id,$tariff_link_id=0,$change_now=0) { //0x301f
            $ret=array(); 
            if (!$this->connection->urfa_call(0x301f)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id); 
            $packet->DataSetInt($account_id); 
            $packet->DataSetInt($tariff_current); 
            $packet->DataSetInt($tariff_next); 
            $packet->DataSetInt($discount_period_id); 
            $packet->DataSetInt($tariff_link_id); 
            $packet->DataSetInt($change_now); 
            $this->connection->urfa_send_param($packet); 
            if ($x = $this->connection->urfa_get_data()) { 
                $ret['tariff_link_id']=$x->DataGetInt(); 
            } 
            return $ret; 
        } 


        function rpcf_remove_tariff($tariff_id) { //0x301b
            $ret=1;
            if (!$this->connection->urfa_call(0x301b)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($tariff_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret = $x->DataGetInt();
            }
            return $ret;
        }

        function rpcf_remove_user_from_group($user_id,$group_id) { //0x2408
            if (!$this->connection->urfa_call(0x2408)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($group_id);
            $this->connection->urfa_send_param($packet);
        }

        function rpcf_get_accountinfo($account_id) { //0x15019
            $ret=array();
            if (!$this->connection->urfa_call(0x15109)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($account_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret['is_blocked']=$x->DataGetInt();
                $ret['vat_rate']=$x->DataGetDouble();
                $ret['sale_tax_rate']=$x->DataGetDouble();
                $ret['credit']=$x->DataGetDouble();
                $ret['balance']=$x->DataGetDouble();
                $ret['int_status']=$x->DataGetInt();
                $ret['unlimited']=$x->DataGetInt();
                $ret['auto_enable_inet']=$x->DataGetInt();
                $ret['external_id']=$x->DataGetString();
            }
            return $ret;
        }


        function rpcf_save_account($account) { //0x1510b
            if (!$this->connection->urfa_call(0x1510b)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            if (!$account['block_start_date']) $account['block_start_date'] = now(); 
            if (!$account['block_end_date']) $account['block_end_date'] = max_time(); 
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($account['account_id']); 
            $packet->DataSetDouble($account['credit']); 
            $packet->DataSetInt($account['is_blocked']); 
            if ($account['is_blocked'] !=0 ) { 
                $packet->DataSetInt($account['block_start_date']); 
                $packet->DataSetInt($account['block_end_date']); 
            }               
            $packet->DataSetDouble($account['vat_rate']); 
            $packet->DataSetDouble($account['sale_tax_rate']); 
            $packet->DataSetInt($account['int_status']); 
            $packet->DataSetInt($account['unlimited']); 
            $packet->DataSetInt($account['auto_enable_inet']); 
            $packet->DataSetString($account['external_id']); 
            $this->connection->urfa_send_param($packet); 
            $this->connection->urfa_get_data();
            return TRUE;
        } 

        function rpcf_search_users_light($login="%",$email="%",$fname="%") { //0x1202 
            $ret=array(); 
            if (!$this->connection->urfa_call(0x1202)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetString($login); 
            $packet->DataSetString($email); 
            $packet->DataSetString($fname); 
            $this->connection->urfa_send_param($packet); 
            if ($x = $this->connection->urfa_get_data()){ 
                $ret['success'] = $x->DataGetInt(); 
                $ret['total'] = $x->DataGetInt(); 
                $ret['show_count'] = $x->DataGetInt(); 
                if($ret['show_count']>0){ 
                    for($i=0;$i<=$ret['show_count']-1;$i++){ 
                        $ret['list'][$i]['id']= $x->DataGetInt(); 
                        $ret['list'][$i]['login']= $x->DataGetString(); 
                        $ret['list'][$i]['email']= $x->DataGetString(); 
                        $ret['list'][$i]['fname']= $x->DataGetString(); 
                    } 
                } 
            } 
            return $ret; 
        } 

        function rpcf_unlink_user_tariff($user_id,$account_id=0,$tariff_link_id=0) { //0x3019 
            $ret=array(); 
            if (!$this->connection->urfa_call(0x3019)) { 
                print "Error calling function ". __FUNCTION__ ."\n"; 
                return FALSE; 
            } 
            $packet = $this->connection->getPacket(); 
            $packet->DataSetInt($user_id); 
            $packet->DataSetInt($account_id); 
            $packet->DataSetInt($tariff_link_id); 
            $this->connection->urfa_send_param($packet); 
            $this->connection->urfa_get_data();
            return $ret; 
        }


        function rpcf_get_free_ips_for_house($house_id) { //0x15101
            $ret=array();
            if (!$this->connection->urfa_call(0x15101)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($house_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret['ips_size']=$x->DataGetInt();
                for ($i=0;$i<$ret['ips_size'];$i++) {
                    $set['ip_address']=$x->DataGetIP46Address();
                    $set['mask']=$x->DataGetInt();
                    $set['zone_name']=$x->DataGetString();
                    $ret['free_ips'][]=$set;
                }
            }
            return $ret;
        }


        function rpcf_whoami() { //0x4417
            $ret=array();
            if (!$this->connection->urfa_call(0x4417)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();
            $ret['my_uid']=$x->DataGetInt();
            $ret['login']=$x->DataGetString();
            $ret['user_ip4']=$x->DataGetIP46Address();
            $ret['user_mask4']=$x->DataGetInt();
            $ret['user_ip6']=$x->DataGetIP46Address();
            $ret['user_mask6']=$x->DataGetInt();

            $count=$x->DataGetInt();
            $ret['system_group_size']=$count;

            for ($i=0; $i < $count; $i++ ) {
                $list['system_group_id']=$x->DataGetInt();
                $list['system_group_name']=$x->DataGetString();
                $list['system_group_info']=$x->DataGetString();
                $ret['system_groups'][]=$list;
            }

            $count=$x->DataGetInt();
            $ret['allowed_fids_size']=$count;
            for ($i=0; $i < $count; $i++ ) {
                $list['id']=$x->DataGetInt();
                $list['name']=$x->DataGetString();
                $list['module']=$x->DataGetString();
                $ret['allowed_fids'][]=$list;
            }

            $count=$x->DataGetInt();
            $ret['not_allowed_size']=$count;

            for ($i=0; $i < $count; $i++ ) {
                $list['id_not_allowed']=$x->DataGetInt();
                $list['name_not_allowed']=$x->DataGetString();
                $list['module_not_allowed']=$x->DataGetString();
                $ret['not_allowed_fids'][]=$list;
            }

            return $ret;
        }


        function rpcf_get_core_time() { //0x11112
            $ret=array();
            if (!$this->connection->urfa_call(0x11112)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }

            if ($x = $this->connection->urfa_get_data()){
                $ret['time']=$x->DataGetInt();
                $set['tzname']=$x->DataGetString();
            }
            return $ret;
        }

        function rpcf_delete_slink($slink_id) { //0x5100
            if (!$this->connection->urfa_call(0x5100)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($slink_id);
            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()) {
                $ret=$x->DataGetInt();
            } else {
                return -1; // unable to delete service link
            }
            return $ret;
        }

        function rpcf_search_users_new($poles,$patterns,$sel_type) { //0x1206
            $ret=array();
            if (!$this->connection->urfa_call(0x1206)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt(count($poles));
            for ($i=0; $i<count($poles);$i++){
                $packet->DataSetInt($poles[$i]);
            };
            $packet->DataSetInt($sel_type);
            $pat_count=count($patterns);
            $packet->DataSetInt($pat_count);
            for ($i=0;$i<count($patterns);$i++){
                $packet->DataSetInt($patterns[$i]['what_id']);
                $packet->DataSetInt($patterns[$i]['criteria_id']);
                if ($patterns[$i]['what_id']==33) {
                    $packet->DataSetInt($patterns[$i]['pattern']);
                }else{
                    $packet->DataSetString($patterns[$i]['pattern']);

                }
            }

            $this->connection->urfa_send_param($packet);
            if ($x = $this->connection->urfa_get_data()){
                $ret['user_data_size']=$x->DataGetInt();
                for ($i=0;$i<$ret['user_data_size'];$i++){
                    $ret[$i]['user_id']=$x->DataGetInt();
                    $ret[$i]['login']=$x->DataGetString();
                    $ret[$i]['basic_account']=$x->DataGetInt();
                    $ret[$i]['full_name']=$x->DataGetString();
                    $ret[$i]['is_blocked']=$x->DataGetInt();
                    $ret[$i]['balance']=$x->DataGetDouble();
                    $ret[$i]['ip_address_size']=$x->DataGetInt();
                    for ($j=0;$j<$ret[$i]['ip_address_size'];$j++){
                        $ret[$i]['ip_address'][$j]['ip_group_size']=$x->DataGetInt();
                        for ($k=0;$k<$ret[$i]['ip_address'][$j]['ip_group_size'];$k++){
                            $ret[$i]['ip_address'][$j]['ip_group'][$k]['type']=$x->DataGetInt();
                            $ret[$i]['ip_address'][$j]['ip_group'][$k]['ip']=$x->DataGetIP46Address();
                            $ret[$i]['ip_address'][$j]['ip_group'][$k]['mask']=$x->DataGetInt();
                        }
                    }

                    for ($j=0;$j<count($poles);$j++){
                        if ($poles[$j]==4){$ret[$i]['discount_period_id']=$x->DataGetInt();}
                        if ($poles[$j]==6){$ret[$i]['create_date']=$x->DataGetInt();}
                        if ($poles[$j]==7){$ret[$i]['last_change_date']=$x->DataGetInt();}
                        if ($poles[$j]==8){$ret[$i]['who_create']=$x->DataGetInt();}
                        if ($poles[$j]==9){$ret[$i]['who_change']=$x->DataGetInt();}
                        if ($poles[$j]==10){$ret[$i]['is_juridical']=$x->DataGetInt();}
                        if ($poles[$j]==11){$ret[$i]['juridical_address']=$x->DataGetString();}
                        if ($poles[$j]==12){$ret[$i]['actual_address']=$x->DataGetString();}
                        if ($poles[$j]==13){$ret[$i]['work_telephone']=$x->DataGetString();}
                        if ($poles[$j]==14){$ret[$i]['home_telephone']=$x->DataGetString();}
                        if ($poles[$j]==15){$ret[$i]['mobile_telephone']=$x->DataGetString();}
                        if ($poles[$j]==16){$ret[$i]['web_page']=$x->DataGetString();}
                        if ($poles[$j]==17){$ret[$i]['icq_number']=$x->DataGetString();}
                        if ($poles[$j]==18){$ret[$i]['tax_number']=$x->DataGetString();}
                        if ($poles[$j]==19){$ret[$i]['kpp_number']=$x->DataGetString();}
                        if ($poles[$j]==21){$ret[$i]['house_id']=$x->DataGetInt();}
                        if ($poles[$j]==22){$ret[$i]['flat_number']=$x->DataGetString();}
                        if ($poles[$j]==23){$ret[$i]['entrance']=$x->DataGetString();}
                        if ($poles[$j]==24){$ret[$i]['floor']=$x->DataGetString();}
                        if ($poles[$j]==25){$ret[$i]['email']=$x->DataGetString();}
                        if ($poles[$j]==26){$ret[$i]['passport']=$x->DataGetString();}
                        if ($poles[$j]==40){$ret[$i]['district']=$x->DataGetString();}
                        if ($poles[$j]==41){$ret[$i]['building']=$x->DataGetString();}
                        if ($poles[$j]==44){$ret[$i]['external_id']=$x->DataGetString();}
                    }
                }
            }
            return $ret;
        }

        function rpcf_generate_doc_for_user($doc_type_id,$acc_id,$template_id) { //0x7030
            $ret=array();
            if (!$this->connection->urfa_call(0x7030)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet=$this->connection->getPacket();
            $packet->DataSetInt($doc_type_id);
            $packet->DataSetInt(0);
            $packet->DataSetInt($acc_id);
            $packet->DataSetInt($template_id);
            $this->connection->urfa_send_param($packet);
            if($x = $this->connection->urfa_get_data())
            {// 
                $ret['template_id']=$x->DataGetInt();
                $ret['static_id']=$x->DataGetInt();
                if ($ret['static_id']!=0){

                    $count = $x->DataGetInt();
                    $ret['count'] = $count;
                    for ($i=0;$i<$count;$i++)
                    {
                        $ret['text'][$i]=$x->DataGetString();
                    }
                    $ret['dynamic_landscape']=$x->DataGetInt();
                }else{

                    $ret['dynamic_id']=$x->DataGetInt();
                    $ret['count']=$x->DataGetInt();
                    for ($i=0;$i<$ret['count'];$i++)
                    {
                        $ret['text'][$i]=$x->DataGetString();
                    }
                    $ret['static_landscape']=$x->DataGetInt();
                }
            }
            return $ret;
        }

        function rpcf_payments_report_owner_ex($time_start=0,$time_end) { //0x300a
            $ret=array();
            if (!$this->connection->urfa_call(0x300a)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet=$this->connection->getPacket();
            $packet->DataSetInt($time_start);
            $packet->DataSetInt($time_end);

            $this->connection->urfa_send_param($packet);
            if($x = $this->connection->urfa_get_data()){//
                $unused=$x->DataGetInt();
                $ret['count']=$x->DataGetInt();
                for($i=0;$i<$ret['count'];$i++){
                    $ret[$i]['id']=$x->DataGetInt();
		    $ret[$i]['account_id']=$x->DataGetInt();
                    $ret[$i]['login']=$x->DataGetString();
                    $ret[$i]['user_id']=$x->DataGetInt();
                    $ret[$i]['full_name']=$x->DataGetString();
                    $ret[$i]['actual_date']=$x->DataGetInt();
                    $ret[$i]['payment_enter_date']=$x->DataGetInt();
                    $ret[$i]['payment']=$x->DataGetDouble();
                    $ret[$i]['payment_incurrency']=$x->DataGetDouble();
                    $ret[$i]['currency_id']=$x->DataGetInt();
                    $ret[$i]['method']=$x->DataGetInt();
                    $ret[$i]['who_received']=$x->DataGetInt();
                    $ret[$i]['admin_comment']=$x->DataGetString();
                    $ret[$i]['payment_ext_number']=$x->DataGetString();
                }

            }
            return $ret;
        }

        function rpcf_payments_report_new($user_id=0,$acccount_id,$group_id=0,$apid=0, $time_start,$time_end) { //0x3030
            $ret=array();
            if (!$this->connection->urfa_call(0x3030)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet=$this->connection->getPacket();
            $packet->DataSetInt($user_id);
            $packet->DataSetInt($acccount_id);
            $packet->DataSetInt($group_id);
            $packet->DataSetInt($apid);
            $packet->DataSetInt($time_start);
            $packet->DataSetInt($time_end);

            $this->connection->urfa_send_param($packet);
            if($x = $this->connection->urfa_get_data()){//
                $unused=$x->DataGetInt();
                $ret['count']=$x->DataGetInt();
                for($i=0;$i<$ret['count'];$i++){
                    $ret[$i]['id']=$x->DataGetInt();
                    $ret[$i]['account_id']=$x->DataGetInt();
                    $ret[$i]['login']=$x->DataGetString();
                    $ret[$i]['actual_date']=$x->DataGetInt();
                    $ret[$i]['payment_enter_date']=$x->DataGetInt();
                    $ret[$i]['payment']=$x->DataGetDouble();
                    $ret[$i]['payment_incurrency']=$x->DataGetDouble();
                    $ret[$i]['currency_id']=$x->DataGetInt();
                    $ret[$i]['method']=$x->DataGetInt();
                    $ret[$i]['who_received']=$x->DataGetInt();
                    $ret[$i]['admin_comment']=$x->DataGetString();
                    $ret[$i]['payment_ext_number']=$x->DataGetString();
                    $ret[$i]['full_name']=$x->DataGetString();
                    $ret[$i]['acc_external_id']=$x->DataGetString();
                    $ret[$i]['burnt_date']=$x->DataGetString();
                }
            }
            return $ret;
        }


        function rpcf_get_houses_list() { //0x2810
            $ret=array();
            if (!$this->connection->urfa_call(0x2810)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();// Tariff count
            $count = $x->DataGetInt();
            $ret['houses_size'] = $count;
            for ($i=0;$i<$count;$i++) {
                $house['house_id']=$x->DataGetInt();
                $house['ip_zone_id']=$x->DataGetInt();
                $house['connect_date']=$x->DataGetInt();
                $house['post_code']=$x->DataGetString();
                $house['country']=$x->DataGetString();
                $house['region']=$x->DataGetString();
                $house['city']=$x->DataGetString();
                $house['street']=$x->DataGetString();
                $house['number']=$x->DataGetString();
                $house['building']=$x->DataGetString();
                $ret['houses'][]=$house;
            }
            return $ret;
        }


        function rpcf_get_house($house_id) { // 0x2812
            $ret=array();
            if (!$this->connection->urfa_call(0x2812)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet = $this->connection->getPacket();
            $packet->DataSetInt($house_id);
            $this->connection->urfa_send_param($packet);

            if ($x = $this->connection->urfa_get_data())
            {
                $ret['house_id'] = $x->DataGetInt();
                $ret['connect_date'] = $x->DataGetInt();
                $ret['post_code'] = $x->DataGetString();
                $ret['country'] = $x->DataGetString();
                $ret['region'] = $x->DataGetString();
                $ret['city'] = $x->DataGetString();
                $ret['street'] = $x->DataGetString();
                $ret['number'] = $x->DataGetString();
                $ret['building'] = $x->DataGetString();
                $ret['count'] = $x->DataGetInt();
                for ($i=0;$i<$ret['count'];$i++) {
                    $ipzone['ipzone_id'] = $x->DataGetInt();
                    $ipzone['ipzone_name'] = $x->DataGetString();
                    $ret['ipzones'][]=$ipzone;
                }
            }
            return $ret;
        }


        function rpcf_get_banks() { //0x6002
            $ret=array();
            if (!$this->connection->urfa_call(0x6002)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();// Tariff count
            $count = $x->DataGetInt();
            $ret['banks_size'] = $count;
            for ($i=0;$i<$count;$i++) {
                $bank['id']=$x->DataGetInt();
                $bank['bic']=$x->DataGetString();
                $bank['name']=$x->DataGetString();
                $bank['city']=$x->DataGetString();
                $bank['kschet']=$x->DataGetString();
                $ret['banks'][]=$bank;
            }
            return $ret;
        }

        function rpcf_get_doc_types_list() { //0x7024
            $ret=array();
            if (!$this->connection->urfa_call(0x7024)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $x = $this->connection->urfa_get_data();// Types count
            $count = $x->DataGetInt();
            $ret['count'] = $count;
            for ($i=0;$i<$count;$i++) {
                $type['doc_name']=$x->DataGetString();
                $type['id']=$x->DataGetInt();

                $ret['doc_types'][]=$type;
            }
            return $ret;
        }

        function rpcf_get_doc_templates_list($doc_type_id) { //0x7022
            $ret=array();
            if (!$this->connection->urfa_call(0x7022)) {
                print "Error calling function ". __FUNCTION__ ."\n";
                return FALSE;
            }
            $packet=$this->connection->getPacket();
            $packet->DataSetInt($doc_type_id);
            $this->connection->urfa_send_param($packet);
            $x = $this->connection->urfa_get_data();// Tariff count
            $count = $x->DataGetInt();
            $ret['count'] = $count;
            for ($i=0;$i<$count;$i++) {
                $doc_template['id']=$x->DataGetInt();
                $doc_template['doc_id']=$x->DataGetInt();
                $doc_template['date']=$x->DataGetInt();
                $doc_template['doc_name']=$x->DataGetString();
                $doc_template['def']=$x->DataGetInt();

                $ret['doc_templates'][]=$doc_template;
            }
            return $ret;
        }

    }

?>
