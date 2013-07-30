<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Default_ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $this->view->title = "Что-то пошло не так";
        $this->view->headTitle($this->view->title, 'PREPEND');

        $errors = $this->_getParam('error_handler');

        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Страница не найдена (Page not found)';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Ошибка приложения';
                break;
        }

        $this->view->exceptionMessage = $errors->exception->getMessage();
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == TRUE) {
            $this->view->exception = $errors->exception;
        }
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message);
            $log->crit($errors->exception);
            foreach ($errors->request->getParams() as $key=> $value) {
                $log->crit($key . ' => ' . $value);
            }
        }
        $this->view->request = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return FALSE;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}