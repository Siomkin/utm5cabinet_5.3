<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_Card extends Zend_Form
{
    public function __construct($accounts)
    {

        $this->addElement(
            'select', 'account', array(
                                      'label'        => 'Аккаунт',
                                      'class'        => 'form-control col-md-4',
                                      'required'     => TRUE,
                                      'filters'      => array('StringTrim', 'StripTags'),
                                      'multioptions' => $accounts,
                                      'validators'   => array(
                                          array('InArray',
                                                FALSE,
                                                array(array_keys($accounts)))
                                      ),
                                 )
        );

        $this->addElement(
            'text', 'card', array(
                                 'label'      => 'Карта',
                                 'class'      => 'form-control col-md-4',
                                 'required'   => TRUE,
                                 'filters'    => array('StringTrim', 'StripTags'),
                                 'validators' => array('Int')

                            )
        );
        $this->addElement(
            'text', 'pin', array(
                                'label'      => 'ПИН',
                                'class'      => 'form-control col-md-4',
                                'required'   => TRUE,
                                'filters'    => array('StringTrim', 'StripTags'),
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


        parent::__construct();
    }

    public function init()
    {
        $this->addAttribs(array('class'=>'well col-md-4'));
    }
}