<?php

/**
 * Class to work this the Zend_Db_Table
 *
 * @author darang
 */
abstract class DRG_Model
{

    /**
     * Object of Zend_Db_Table_Abstract
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Object of Zend_Db_Table_Row
     *
     * @var Zend_Db_Table_Row
     */
    protected $_row;

    protected $_dbName;

    /**
     *
     * @param Zend_Db_Table_Abstract $dbTable
     * @param <int> $id
     */
    public function __construct(Zend_Db_Table_Abstract $dbTable, $id = NULL)
    {
        $this->_dbTable = $dbTable;
        $this->_dbName = $dbTable->getDbName();
        if ($id) {
            $this->_row = $this->_dbTable->find($id)->current();
        } else {
            $this->_row = $this->_dbTable->createRow();
        }
    }

    /**
     * Fill the fields off the form
     *
     * @param <array> $data
     */
    public function fill($data)
    {
        foreach ($data as $key => $value) {
            if (isset ($this->_row->$key)) {
                $this->_row->$key = $value;
            }
        }
    }

    /**
     * Save the values
     */
    public function save()
    {
        return $this->_row->save();
    }

    /**
     * Delete the row
     */
    public function delete()
    {
        return $this->_row->delete();
    }

    /**
     * Setters and Getters methods
     *
     * @param <type> $name
     * @param <type> $value
     */
    public function __set($name, $value)
    {
        if (isset ($this->_row->$name)) {
            $this->_row->$name = $value;
        }
    }

    public function __get($name)
    {
        if (isset ($this->_row->$name)) {
            return $this->_row->$name;
        }
    }

    public function getDbName()
    {
        return $this->_dbName;
    }

}

?>
