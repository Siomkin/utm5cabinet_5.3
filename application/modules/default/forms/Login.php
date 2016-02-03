<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Default_Form_Login extends Zend_Form
{
    public function init()
    {
        $this->setIsArray(TRUE);
        //$this->setElementsBelongTo('bootstrap');

       // $this->_addClassNames('well');

        $this->addElement(
            'text', 'username', array(
                                     'label'      => 'Имя пользователя',
                                     'class'      => 'form-control',
                                     'required'   => TRUE,
                                     'filters'    => array('StringTrim', 'StripTags'),
                                     'validators' => array(
                                         //'alnum',
                                         array('regex', false,
                                         array(
                                             'pattern'   => '/^[a-z0-9\/\\\\~.!-@$%^&*]+$/i',
                                             'messages'  =>  'Не верный формат имени')
                                         )
                                     ),
                                )
        );


        $this->addElement(
            'password', 'password', array(
                                         'label'    => 'Пароль',
                                         'class'    => 'form-control',
                                         'required' => TRUE,
                                         'required' => TRUE,
                                         'filters'  => array('StringTrim', 'StripTags'),
                                         //'validators' => array('alnum'),
                                    )
        );
      /*  $this->addDisplayGroup(
            array('username', 'password'),
            'login',
            array(
                 'legend' => 'Вход в личный кабинет'
            )
        );*/

        $this->addElement(
            'button', 'send', array(
                                   'label'      => 'Войти',
                                   'class'      => 'btn btn-primary',
                                   'type'       => 'submit',
                                   'buttonType' => 'success',
                                   'icon'       => 'ok',
                                   'escape'     => FALSE
                              )
        );

      /*  $this->addDisplayGroup(
            array('send', 'reset'),
            'actions',
            array(
                 'disableLoadDefaultDecorators' => TRUE,
                 'decorators'                   => array('Actions')
            )
        );*/
    }

}