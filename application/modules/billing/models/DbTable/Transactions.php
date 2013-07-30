<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Stat_Model_DbTable_Transactions extends Zend_Db_Table_Abstract
{

    protected $_name = 'discount_transactions_all';

    protected $_referenceMap
        = array(
            'Product' => array(
                'columns'           => array('account_id'),
                'refTableClass'     => 'Stat_Model_DbTable_Users',
                'refColumns'        => array('id')
            )
        );

    public function getDbName()
    {
        return $this->_name;
    }
}