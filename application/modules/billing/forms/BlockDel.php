<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_BlockDel extends Zend_Form
{

    public function __construct()
    {
        $this->setName('form_blockdel');
        $this->setAttrib('class', 'wellform');
        $this->removeDecorator('HtmlTag');
        parent::__construct();

        $act = new Zend_Form_Element_Hidden('act');
        $act->addValidator('Int')
            ->removeDecorator('label')
            ->removeDecorator('HtmlTag');
        $this->addElement($act->setValue(2));

        $this->addElement(
            'checkbox', 'accepted', array(
                                         'label'        => 'Подтвердить удаление',
                                         'required'     => TRUE,
                                         'validators'   => array(
                                             array(new Zend_Validate_InArray(array(1)), FALSE)
                                         ),
                                         'ErrorMessages'=> array('Необходимо согласиться с условиями'),
                                    )
        );

        $submit = new Zend_Form_Element_Submit('submit', array('class'=> 'btn btn-large'));
        $submit->setLabel('Снять блокировку')
            ->removeDecorator('DtDdWrapper');

        $this->addElement($submit);

    }

}