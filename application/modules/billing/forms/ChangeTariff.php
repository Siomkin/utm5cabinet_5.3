<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_ChangeTariff extends Zend_Form
{

    public function __construct($tariffInfo)
    {
        parent::__construct();
        $this->setName('form_change_tariff');
        //$this->removeDecorator('HtmlTag');

        $data = array();

        foreach ($tariffInfo['tariff'] as $tariff) {
            $data [$tariff['id']] = $tariff['name'];
        }

        $this->addElement(
            'radio', 'next_tp', array(
                                     'label'        => 'Следующий тариф',
                                     'required'     => TRUE,
                                     'multioptions' => $data
                                )
        );

        $this->addElement(
            'checkbox', 'accepted', array(
                                         'label'         => 'Подтвердить',
                                         'required'      => TRUE,
                                         'validators'    => array(
                                             array(new Zend_Validate_InArray(array(1)), FALSE)
                                         ),
                                         'ErrorMessages' => array('Необходимо согласиться с условиями'),
                                    )
        );

        $submit = new Zend_Form_Element_Submit('submit', array('class' => 'btn btn-primary'));
        $submit->setLabel('Установить')->removeDecorator('DtDdWrapper');

        $this->addElement($submit);

    }
    public function init()
    {
        $this->addAttribs(array('class' => 'well col-md-6'));
    }

}