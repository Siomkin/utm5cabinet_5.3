<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_Group extends Zend_View_Helper_Abstract
{
    /**
     * @param string $time
     *
     * @return string
     */
    public function group($user_id)
    {

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/group.ini', 'app');

        $sms = new Billing_Model_Users();

        $items = $sms->getBlockInfoForUser($user_id);
        $urfaAdmin = new Urfa_Admin();
        $groups = $this->getGroupsId($urfaAdmin->rpcf_get_groups_for_user($user_id));

        $tmp_user['diff'] = 0;
        foreach ($items as $item) {

//            if ($item['connect_date'] > $item['create_date']) {
//                $tmp_user['connect_date'] = $item['create_date'];
//            } else {
            $tmp_user['connect_date'] = $item['connect_date'];
//            }

            if (!is_null(['is_deleted'])) {
                if ($item['is_deleted'] === 0) {
                    $diff = time() - $item['start_date'];
                } else {
                    $diff = 0;
                    if($item['expire_date']!=2000000000){
                        $diff = $item['expire_date'] - $item['start_date'];
                    }

                }
                if($diff > 0) {
                    $tmp_user['diff'] += $diff;
                }

            }
        }
        $time = $tmp_user['connect_date'] + $tmp_user['diff'];
        //$urfaAdmin = new Urfa_Admin();


        if (in_array($config->group->groupId_3, $groups)) {
            $text = $config->group->max;
        } elseif (in_array($config->group->groupId_2, $groups)) {
            $text = '<p>У Вас 2 группа тарифов.</p>';
            $text .= '<span class="timeago">' . $config->group->next . '
			<time class="timeago" datetime="' . date('Y-m-d H:i:s', $time + $config->group->time_3 * 30 * 24 * 60 * 60) . '" title="' . date('Y-m-d H:i:s', $time + $config->group->time_3 * 30 * 24 * 60 * 60) . '">July 17, 2008</time>

			<abbr class="timefuture" datetime="' . date('Y-m-d H:i:s', $time + $config->group->time_3 * 30 * 24 * 60 * 60) . '"  title="' . date('Y-m-d H:i:s', $time + $config->group->time_3 * 30 * 24 * 60 * 60) . '">'
                . date('d.m.Y H:i:s', $time + $config->group->time_3 * 30 * 24 * 60 * 60) . '</abbr></span>';
        } elseif (in_array($config->group->groupId_1, $groups)) {
            $text = '<p>У Вас 1 группа тарифов.</p>';
            $text .= '<span class="timeago">' . $config->group->next . ' <abbr class="timefuture" datetime="' . date('Y-m-d H:i:s', $time + $config->group->time_2 * 30 * 24 * 60 * 60) . '"  title="' . date('Y-m-d H:i:s', $time + $config->group->time_2 * 30 * 24 * 60 * 60) . '">'
                . date('d.m.Y H:i:s', $time + $config->group->time_2 * 30 * 24 * 60 * 60) . '</abbr></span>';
        } else {
            $text = '<p>У Вас нет группы тарифов.</p>';
            $text .= '<span class="timeago">' . $config->group->next . ' <abbr class="timefuture" datetime="' . date('Y-m-d H:i:s', $time + $config->group->time_1 * 30 * 24 * 60 * 60) . '"  title="' . date('Y-m-d H:i:s', $time + $config->group->time_1 * 30 * 24 * 60 * 60) . '">'
                . date('d.m.Y H:i:s', $time + $config->group->time_1 * 30 * 24 * 60 * 60) . '</abbr></span>';
        }


        $output = '';
        if ($text) {
            // $time = new DateTime();
            // $time->setTimestamp($cache['mtime']);

            $output = '<script type="text/javascript">';
            $output .= 'jQuery(document).ready(function() { jQuery("abbr.timefuture").timeago();});';
            $output .= '</script>';

            $output .= $text;

        }

        return $output;
    }

    function getGroupsId($groups)
    {
        $groupsId = false;
        if ($groups['groups_size'] > 0) {

            foreach ($groups['group'] as $group) {
                $groupsId[] = $group['group_id'];
            }
        }
        return $groupsId;
    }
}