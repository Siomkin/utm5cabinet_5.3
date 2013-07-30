<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Model_DbTable_Users extends Zend_Db_Table_Abstract
{

    protected $_name = 'users';
    protected $_dependentTables = array('discount_transactions_all');

    public function getDbName()
    {
        return $this->_name;
    }
}
