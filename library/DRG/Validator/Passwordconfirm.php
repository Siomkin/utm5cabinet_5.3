<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_Validator_Passwordconfirm extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates
        = array(
            self::NOT_MATCH => 'Пароли не совпадают.'
        );

    public function isValid($value, $context = NULL)
    {
        $value = (string)$value;

        if (is_array($context)) {
            if (isset($context['new_password'])
                && ($value == $context['new_password'])
            ) {
                return TRUE;
            }
        } elseif (is_string($context) && ($value = $context)) {
            return TRUE;
        }

        $this->_error(self::NOT_MATCH);
        return FALSE;
    }
}


?>