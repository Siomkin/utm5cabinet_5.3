<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_Balance extends Zend_View_Helper_Abstract
{
    public function balance($balance = '', $credit = FALSE, $currency)
    {
        if ($balance > 0) {
            $style = 'badge-success';
        } elseif ($balance + $credit > 0) {
            $style = 'badge-warning';
        } elseif ($balance == 0) {
            $style = '';
        }
        else {
            $style = 'badge-important';
        }
        $output = '<span class="badge ' . $style . '">' . $balance;
        if ($credit) {
            $output .= ' (+' . $credit . ')';
        }
        $output .= ' '.$currency.'</span>';

        return $output;
    }
}