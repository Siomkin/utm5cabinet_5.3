<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_Card extends Twitter_Bootstrap_Form_Horizontal
{
    public function __construct($accounts)
    {

        $this->addElement(
            'select', 'account', array(
                                      'label'        => 'Аккаунт',
                                      'class'        => 'focused span3',
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
                                 'class'      => 'span3',
                                 'required'   => TRUE,
                                 'filters'    => array('StringTrim', 'StripTags'),
                                 'validators' => array('Int')

                            )
        );
        $this->addElement(
            'text', 'pin', array(
                                'label'      => 'ПИН',
                                'class'      => 'span3',
                                'required'   => TRUE,
                                'filters'    => array('StringTrim', 'StripTags'),
                           )
        );

        $this->addElement(
            'button', 'send', array(
                                   'label'      => 'Отправить',
                                   'class'      => 'btn btn-large',
                                   'type'       => 'submit',
                                   'buttonType' => 'success',
                                   'icon'       => 'ok',
                                   'escape'     => FALSE
                              )
        );

        $this->addDisplayGroup(
            array('send', 'reset'),
            'actions',
            array(
                 'disableLoadDefaultDecorators' => TRUE,
                 'decorators'                   => array('Actions')
            )
        );

        parent::__construct();
    }

    public function init()
    {
        // $this->setName('credit_form');

        $this->_addClassNames('well');

    }
}