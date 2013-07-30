<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_Traffic extends Zend_Form
{

    public function __construct($startDate = FALSE, $endDate = FALSE)
    {
        $this->setName('form_traffic');
        $this->setAttrib('class', 'wellform');
        $this->removeDecorator('HtmlTag');
        parent::__construct();

        if (!$startDate) {
            $start_Date = date('01.m.Y', time());
        } else {
            $start_Date = date('d.m.Y', $startDate);
        }

        if (!$endDate) {
            $end_Date = date('01.m.Y', time());
        } else {
            $end_Date = date('d.m.Y', $endDate);
        }

        $startDate = new ZendX_JQuery_Form_Element_DatePicker('startDate');
        $startDate->setLabel('C')
            ->removeDecorator('label')
            ->removeDecorator('HtmlTag')
            ->setJQueryParam('defaultDate', $start_Date)
            ->setJQueryParam('dateFormat', 'dd.mm.yy')
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('regional', 'ru')
            ->setJqueryParam('showOtherMonths', 'true')
            ->setJqueryParam('selectOtherMonths', 'true')
            ->addValidator(
            new Zend_Validate_Date(
                array('format' => 'dd.mm.yy'))
        )
            ->setRequired(TRUE);

        $endDate = new ZendX_JQuery_Form_Element_DatePicker('endDate');
        $endDate->setLabel('по')
            ->removeDecorator('label')
            ->removeDecorator('HtmlTag')
            ->setJQueryParam('dateFormat', 'dd.mm.yy')
            ->setJQueryParam('defaultDate', $end_Date)
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('showOtherMonths', 'true')
            ->setJqueryParam('selectOtherMonths', 'true')
            ->setJqueryParam('regional', 'ru')
            ->addValidator(
            new Zend_Validate_Date(
                array('format' => 'dd.mm.yy'))
        )
            ->setRequired(TRUE);

        $serviceType = new Zend_Form_Element_Select('serviceType');
        $serviceType->setLabel('Кол-во объявлений')
            ->removeDecorator('label')
            ->removeDecorator('HtmlTag')
            ->addFilter('StringTrim')
            ->addFilter('StripTags')
            ->addMultiOption('1', 'Общий')
            ->addMultiOption('2', 'По дням')
            ->addMultiOption('3', 'По IP')
            ->addValidator(new Zend_Validate_InArray(array(1, 2, 3)), FALSE);

        $submit = new Zend_Form_Element_Submit('submit', array('class' => 'btn btn-large'));
        $submit->setLabel('Показать')
            ->removeDecorator('DtDdWrapper');
        $this->addElements(
            array($startDate->setValue($start_Date), $endDate->setValue($end_Date), $serviceType, $submit)
        );

    }

}