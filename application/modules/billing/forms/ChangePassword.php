<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Billing_Form_ChangePassword extends Zend_Form
{
    public function __construct()
    {
        parent::__construct();

        $this->addElement(
            'text', 'old_password', array(
                'label' => 'Старый пароль',
                'class' => 'form-control',
                'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'validators' => array(
                    'NotEmpty'
                ),
            )
        );

        $this->addElement(
            'password', 'new_password', array(
                'label' => 'Новый пароль',
                'class' => 'form-control',
                'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'validators' => array(
                    'NotEmpty',
                    array('stringLength', false, array(6))
                ),
            )
        );

        $this->addElement(
            'password', 'new_password_repeat', array(
                'label' => 'Повторите новый пароль',
                'class' => 'form-control',
                'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'prefixPath' => array(
                    'validate' => array(
                        'prefix' => 'DRG_Validator',
                        'path' => 'DRG/Validator',
                    ),
                ),
                'validators' => array(
                    'NotEmpty',
                    'Passwordconfirm'
                ),
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
        $this->addAttribs(array('class' => 'well col-md-6'));
    }
}