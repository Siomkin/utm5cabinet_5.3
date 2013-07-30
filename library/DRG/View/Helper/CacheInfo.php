<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_CacheInfo extends Zend_View_Helper_Abstract
{
    public function cacheInfo($cache = FALSE)
    {
        $output = '';

        if (is_array($cache)) {
            $output
                .= '<br/><div class="alert alert-block fade in alert-sign"><a class="close" data-dismiss="alert">×</a><i class="icon-info-sign"></i>
                 По состоянию на ' . date('H:i:s d.m.Y', $cache['mtime']) . ' (обновится после ' . date(
                'H:i:s', $cache['expire']
            ) . ')</div>';
        }
        return $output;
    }
}