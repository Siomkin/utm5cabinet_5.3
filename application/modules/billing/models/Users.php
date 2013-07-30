<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Model_Users extends DRG_Model
{
    public function __construct($id = NULL)
    {
        parent::__construct(new Billing_Model_DbTable_Users(), $id);
    }

    public function getUserInfo($userAccount)
    {
        return $result = $this->_dbTable->fetchRow(
            $this->_dbTable->select()
                ->setIntegrityCheck(FALSE)
                ->from('users')
                ->join(
                'accounts', 'users.basic_account = accounts.id', array('balance', 'credit', 'is_blocked', 'int_status')
            )
                ->where('users.basic_account = ?', $userAccount)
        );
    }

    public function getUserServices($userAccount, $serviceType = 2)
    {
        return $result = $this->_dbTable->fetchAll(
            $this->_dbTable->select()
                ->setIntegrityCheck(FALSE)
                ->from('service_links', 'is_deleted')
                ->join(
                'services_data', 'services_data.id = service_links.service_id', array('service_name', 'service_type')
            )
                ->join('users', 'service_links.account_id = users.basic_account', array(''))
                ->where('users.basic_account = ?', $userAccount)
                ->where('services_data.service_type= ?', $serviceType)
        );
    }

    public function getUserGroups($userAccount)
    {
        return $result = $this->_dbTable->fetchAll(
            $this->_dbTable->select()
                ->setIntegrityCheck(FALSE)
                ->from('users_groups_link', '')
                ->join('groups', 'users_groups_link.group_id = groups.id', array('group_name'))
                ->join('users', 'users_groups_link.user_id = users.id', array(''))
                ->where('users.basic_account = ?', $userAccount)
        );
    }

    /**
     * Возвращает данные о сервисах оказанных пользователю
     * @param int $userAccount
     * @param int $serviceType
     */
    public function getUserServicesData($userAccount, $serviceType = 1)
    {
        return $result = $this->_dbTable->fetchAll(
            $this->_dbTable->select()
                ->setIntegrityCheck(FALSE)
                ->from('discount_transactions_all', array('discount', 'discount_date'))
                ->join('users', 'discount_transactions_all.account_id = users.basic_account', '')
                ->join(
                'services_data', 'discount_transactions_all.service_id = services_data.id',
                array('service_name', 'comment')
            )
                ->where('users.basic_account = ?', $userAccount)
                ->where('services_data.service_type= ?', $serviceType)
        );
    }

    /**
     * Возвращает данные о платежах
     * @param      $userAccount
     * @param null $start_date
     * @param null $end_date
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getUserPaymentData($userAccount, $start_date = NULL, $end_date = NULL)
    {
        $select = $this->_dbTable->select()
            ->setIntegrityCheck(FALSE)
            ->from('payment_transactions')
            ->join('users', 'payment_transactions.account_id = users.basic_account', '')
            ->where('users.basic_account = ?', $userAccount)
            ->order('actual_date DESC');
        $result = $this->_dbTable->fetchAll($select);
        return $result;
    }
}

?>
