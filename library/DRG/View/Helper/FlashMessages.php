<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
    public function flashMessages()
    {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $output = '';

        if (!empty($messages)) {

            foreach ($messages as $message) {
                $output .= '<div class="alert alert-block fade in alert-' . key($message)
                    . '"><a class="close" data-dismiss="alert">×</a><i class="icon-info-sign"></i> ' . current($message)
                    . '</div>';
            }
        }
        return $output;
    }
}