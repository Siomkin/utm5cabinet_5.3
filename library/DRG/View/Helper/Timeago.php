<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_Timeago extends Zend_View_Helper_Abstract
{
    /**
     * @param string $time
     *
     * @return string
     */
    public function timeago($cache = null)
    {
        $output = '';
        if (is_array($cache)) {
           // $time = new DateTime();
           // $time->setTimestamp($cache['mtime']);

            $output= '<script type="text/javascript">';
            $output.= 'jQuery(document).ready(function() {
                        jQuery("time.timeago").timeago();
                    });';
            $output .= '</script>';

            $output .= '<span class="timeago">обновлено <time class="timeago" datetime="' . date('Y-m-d H:i:s',$cache['mtime']) . '">'
                . date('d.m.Y H:i:s',$cache['mtime']) . '</time></span>';

        }

        return $output;
    }
}