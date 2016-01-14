<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_UserEdit extends Zend_Form
{

    public function __construct()
    {
        $this->setName('form_useredit');
       // $this->setAttrib('class', 'wellform');
        //$this->removeDecorator('HtmlTag');
        parent::__construct();

/*        $serviceType = new Zend_Form_Element_Text('fullname');
        $serviceType->setLabel('Имя пользователя')
            ->removeDecorator('HtmlTag')
            ->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($serviceType);*/

        $this->addElement(
            'text', 'home_telephone', array(
                'label' => 'Домашний телефон',
                'class' => 'form-control',
                //'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'validators' => array(
                   // 'NotEmpty'
                )
            )
        );
        $this->addElement(
            'text', 'mobile_telephone', array(
                'label' => 'Мобильный телефон',
                'class' => 'form-control',
                //'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'validators' => array(
                   // 'NotEmpty'
                )
            )
        );
        $this->addElement(
            'text', 'email', array(
                'label' => 'Email',
                'class' => 'form-control',
               // 'required' => TRUE,
                'filters' => array('StringTrim', 'StripTags'),
                'validators' => array(
                   // 'NotEmpty',
                    'EmailAddress'
                )
            )
        );


        $accepted = new Zend_Form_Element_Checkbox('accepted');
        $accepted->setLabel('Подтверждаю редактирование')
            ->addValidator(new Zend_Validate_InArray(array(1)), FALSE)
            ->addErrorMessage('Подтвердите правильность информации')
            ->removeDecorator('DtDdWrapper');
        $this->addElement($accepted);

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