<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Billing_Form_Message extends Twitter_Bootstrap_Form_Horizontal
{
    public function __construct()
    {
        parent::__construct();

        $this->addElement(
            'text', 'subject', array(
                                    'label'         => 'Тема',
                                    'placeholder'   => 'Тема',
                                    'class'         => 'focused span4',
                                    'required'      => TRUE,
                                    'filters'       => array('StringTrim', 'StripTags'),
                               )
        );

        $this->addElement(
            'textarea', 'message', array(
                                        'label'         => 'Сообщение',
                                        'placeholder'   => 'Текст сообщения',
                                        'class'         => 'span4',
                                        'required'      => TRUE,
                                        'cols'          => '20',
                                        'rows'          => '6',
                                        'filters'       => array('StringTrim', 'StripTags'),
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
    }

    public function init()
    {
        $this->_addClassNames('well ajax');

        $this->setAction('/user/new-message/');

    }
}