<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_Badge extends Zend_View_Helper_Abstract
{
    /**
     * @param string $text Текст для вывода
     * @param string $type
     * Возможные варианты по умолчанию серый|success(зелёный)|warning(жёлтый)|important(красный)|info(синий)|inverse(чёрный)
     *
     * @return string
     */
    public function badge($text = '', $type = FALSE)
    {
        if ($type) {
            $style = ' badge-' . $type;
        } else {
            $style = '';
        }
        $output = '<span class="badge ' . $style . '">' . $text . '</span>';
        return $output;
    }
}