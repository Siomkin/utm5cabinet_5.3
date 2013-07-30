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
        $this->setAttrib('class', 'wellform');
        $this->removeDecorator('HtmlTag');
        parent::__construct();

        $serviceType = new Zend_Form_Element_Text('fullname');
        $serviceType->setLabel('Имя пользователя')
            ->removeDecorator('HtmlTag')
            ->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($serviceType);

        $serviceType = new Zend_Form_Element_Text('home_telephone');
        $serviceType->setLabel('Домашний телефон')
            ->removeDecorator('HtmlTag')
            ->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($serviceType);

        $serviceType = new Zend_Form_Element_Text('mobile_telephone');
        $serviceType->setLabel('Мобильный телефон')
            ->removeDecorator('HtmlTag')
            ->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($serviceType);

        $serviceType = new Zend_Form_Element_Text('email');
        $serviceType->setLabel('Email')
            ->removeDecorator('HtmlTag')
            ->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($serviceType);

        $accepted = new Zend_Form_Element_Checkbox('accepted');
        $accepted->setLabel('Подтверждаю редактирование')
            ->addValidator(new Zend_Validate_InArray(array(1)), FALSE)
            ->addErrorMessage('Подтвердите правильность информации')
            ->removeDecorator('DtDdWrapper');
        $this->addElement($accepted);


        $submit = new Zend_Form_Element_Submit('submit', array('class' => 'btn btn-large'));
        $submit->setLabel('Отправить')
            ->removeDecorator('DtDdWrapper');
        $this->addElement($submit);

    }

}