<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_Credit extends Zend_Form
{
    public function __construct($max_credit_sum)
    {

        parent::__construct();

        $this->addElement(
            'text', 'credit_sum', array(
                                       'label'      => 'Сумма обещанного платежа',
                                       'class'      => 'form-control',
                                       'required'   => TRUE,
                                       'filters'    => array('StringTrim', 'StripTags'),
                                       'validators' => array(
                                           'Float',
                                           array('Between', FALSE, (array('min' => 0,
                                                                          'max' => $max_credit_sum)))
                                       ),
                                  )
        );

        $this->addElement(
            'checkbox', 'accepted', array(
                                         'label'        => 'Подтвердить',
                                         'required'     => TRUE,
                                         'validators'   => array(
                                             array(new Zend_Validate_InArray(array(1)), FALSE)
                                         ),
                                         'ErrorMessages'=> array('Необходимо согласиться с условиями'),
                                    )
        );

        $this->addElement(
            'button', 'send', array(
                                   'label'      => 'Отправить',
                                   'class'      => 'btn btn-primary',
                                   'type'       => 'submit',
                                   'buttonType' => 'success',
                                   'icon'       => 'ok',
                                   'escape'     => FALSE
                              )
        );

    }

    public function init()
    {
        $this->setIsArray(TRUE);
        $this->addAttribs(array('class'=>'well col-md-6'));

    }
}