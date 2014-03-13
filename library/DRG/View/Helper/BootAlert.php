<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_BootAlert extends Zend_View_Helper_Abstract
{
    public function bootAlert($messages = '')
    {
        $output = '';

        if (is_array($messages)) {
            foreach ($messages as $style=> $message) {
                $output .= '<div class="alert fade in alert-' . $style
                    . '"><a class="close" data-dismiss="alert">×</a><i class="icon-info-sign"></i> ' . $message
                    . '</div>';
            }
        }
        return $output;
    }
}