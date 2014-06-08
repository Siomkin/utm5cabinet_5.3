<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Billing_Form_TurboMode extends Zend_Form
{
    public function __construct()
    {

        parent::__construct();

        $this->addElement(
            'checkbox', 'accepted', array(
                'label' => 'Подтвердить',
                'required' => TRUE,
                'validators' => array(
                    array(new Zend_Validate_InArray(array(1)), FALSE)
                ),
                'ErrorMessages' => array('Необходимо согласиться с условиями'),
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
    }

    public function init()
    {
        $this->setIsArray(TRUE);
        $this->setAttrib('class', 'well');
    }
}