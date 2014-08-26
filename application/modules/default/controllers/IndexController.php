<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Default_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/billing.ini', 'app');
    }

    public function indexAction()
    {
        $this->view->title = "";
        $this->view->headTitle($this->view->title, 'PREPEND');
        $uri = $this->_getParam('return_uri', '/user/');
        $person_type = $this->_getParam('person_type');
        $rs_uri = $this->_getParam('rs_uri');
        if (!is_null($person_type)) {
            $uri .= '?person_type=' . urlencode($person_type);
        }
        if (!is_null($rs_uri)) {
            $uri .= '&rs_uri=' . urlencode($rs_uri);
        }


        if ($this->view->identity != false) {
            $this->redirect('/user/');
        }

        $form = new Default_Form_Login();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {

                $auth = Zend_Auth::getInstance();

                $authAdapter = new Urfa_Auth_Adapter($form->getValue('username'), $form->getValue('password'));
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    // Yay! User is authenticated and stored in the session (via the storage class)
                    $this->redirect($uri);
                } else {
                    $this->_helper->flashMessenger->addMessage(
                        array('error' => 'Ошибка авторизации. Не верная пара логин-пароль')
                    );
                    $this->redirect('/');
                }

            }
        }
        $this->view->form = $form;

        if (!empty($this->config->notice)) {
            $this->view->notice = $this->config->notice;
        }

    }

    public function logoutAction()
    {
        if (!$this->view->identity) {
            $this->_helper->flashMessenger->addMessage(
                array('error' => 'Вам необходимо авторизоваться')
            );
            $this->redirect('/');
        }
        $_auth = Zend_Auth::getInstance();
        $_auth->clearIdentity();
        $this->_helper->flashMessenger->addMessage(
            array('success' => 'До свидания')
        );
        $this->redirect('/');
    }
}



