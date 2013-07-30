<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_Payment extends Zend_Form
{

    public function __construct()
    {
        $this->setName('payment_form');
        $this->setAttrib('class', 'well');
        $this->setAttrib('target', '_blank');
        $this->removeDecorator('HtmlTag');
        parent::__construct();


        $sum = new Zend_Form_Element_Text('sum');
        $sum->setLabel('Сумма')
            ->removeDecorator('HtmlTag')
            ->addValidator('Float')
            ->setRequired(TRUE);


        $submit = new Zend_Form_Element_Submit('submit', array('class'=> 'btn btn-large'));
        $submit->setLabel('Показать квитанцию')
            ->removeDecorator('DtDdWrapper');
        $this->addElements(array($sum, $submit));

    }

}