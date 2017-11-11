<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_Block extends Zend_Form
{

    public function __construct($startDate = FALSE, $endDate = FALSE)
    {
        $this->setName('form_block');
        $this->setAttrib('class', 'well col-md-6');
        $this->removeDecorator('HtmlTag');
        parent::__construct();

        if (!$startDate) {
            $start_Date = date('d.m.Y', time() + 24 * 60 * 60);
        } else {
            $start_Date = date('d.m.Y', $startDate);
        }

        if ($endDate) {
            $end_Date = date('d.m.Y', $endDate);
        } else {
            $end_Date = NULL;
        }

        $act = new Zend_Form_Element_Hidden('act');
        $act->addValidator('Int');
        $this->addElement($act->setValue(1));

        $startDate = new ZendX_JQuery_Form_Element_DatePicker('startDate');
        $startDate->setLabel('Начало действия блокировки')
            ->setAttribs(array('class' => 'form-control'))
            ->setJQueryParam('defaultDate', $start_Date)
            ->setJQueryParam('dateFormat', 'dd.mm.yy')
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('regional', 'ru')
            ->setJqueryParam('showOtherMonths', 'true')
            ->setJqueryParam('selectOtherMonths', 'true')
            ->addValidator(
                new Zend_Validate_Date(
                    array('format' => 'dd.mm.yy')
                )
            )
            ->setRequired(TRUE);

        $endDate = new ZendX_JQuery_Form_Element_DatePicker('endDate');
        $endDate->setLabel('Окончание действия блокировки')
            ->setAttribs(array('class' => 'form-control'))
            ->setJQueryParam('dateFormat', 'dd.mm.yy')
            ->setJQueryParam('defaultDate', $end_Date)
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('showOtherMonths', 'true')
            ->setJqueryParam('selectOtherMonths', 'true')
            ->setJqueryParam('regional', 'ru')
            ->addValidator(
                new Zend_Validate_Date(
                    array('format' => 'dd.mm.yy')
                )
            )
            ->setRequired(TRUE);

        $this->addElements(array($startDate->setValue($start_Date), $endDate->setValue($end_Date)));


        $this->addElement(
            'checkbox',
            'accepted',
            array(
                'label' => 'Подтвердить',
                //'class' => 'focused span3',
                'required' => TRUE,
                'validators' => array(
                    array(new Zend_Validate_InArray(array(1)), FALSE)
                ),
                'ErrorMessages' => array('Необходимо согласиться с условиями'),
            )
        );

        $submit = new Zend_Form_Element_Submit('submit', array('class' => 'btn btn-primary'));
        $submit->setLabel('Установить')
            ->removeDecorator('DtDdWrapper');

        $this->addElement($submit);

    }

}