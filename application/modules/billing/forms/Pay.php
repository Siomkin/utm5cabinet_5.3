<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Billing_Form_Pay extends Zend_Form
{
    public function __construct($accounts)
    {
        $this->setAttrib('class', 'well');
        $this->addElement(
            'select', 'account', array(
                'label' => 'Аккаунт',
                'class' => 'form-control',
                'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'multioptions' => $accounts,
                'validators' => array(
                    array('InArray',
                        FALSE,
                        array(array_keys($accounts)))
                ),
            )
        );

        $this->addElement(
            'text', 'sum', array(
                'label' => 'Сумма',
                'class' => 'form-control',
                'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'validators' => array('Float')

            )
        );

        $this->addElement(
            'button', 'send', array(
                'label' => 'Отправить',
                'class' => 'btn btn-primary',
                'type' => 'submit',
                'buttonType' => 'success',
                'icon' => 'ok',
                'escape' => FALSE
            )
        );

        parent::__construct();
    }

}