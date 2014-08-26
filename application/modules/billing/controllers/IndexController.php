<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2013
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Billing_IndexController extends Zend_Controller_Action
{
    protected $cache;
    protected $config;

    protected $start_day;
    protected $end_day;

    protected $basic_account;
    protected $cache_basic_account;

    public function init()
    {
        if ($this->view->identity == false) {
            $this->_helper->flashMessenger->addMessage(
                array('error' => 'Вам необходимо авторизоваться')
            );
            $uri = '';
            $person_type = $this->_getParam('person_type');
            $rs_uri = $this->_getParam('rs_uri');

            if (!is_null($person_type)) {
                $uri = '?person_type=' . urlencode($person_type);
            }
            if (!is_null($rs_uri)) {
                $uri .= '&rs_uri=' . urlencode($rs_uri);
            }
            $this->redirect('/?return_uri=' . $this->view->url() . $uri);
        }

        $this->config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/billing.ini', 'app');

        $this->view->currencyName = $this->config->currency->name;


        //Инициализируем кэш
        //папка для хранения кэша
        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/' . $this->config->cache->cache_dir);
        //время жизни (сек), сериализация и логирование
        $frontendOptions = array(
            'lifetime' => $this->config->cache->lifetime,
            'debug_header' => true,
            'logger' => true,
            'automatic_serialization' => true
        );
        //метод храниния кэша. Определяет вторая переменная
        //Из наиболее используемых File, APC, возможен memcached, но там нужны дополнительные параметры
        //читайте документацию по Zend_Cache
        $this->cache = Zend_Cache::factory('Core', $this->config->cache->backend, $frontendOptions, $backendOptions);


        //Первый день месяца
        $this->start_day = date('Y-m', time()) . '-01';
        //Завтра
        $this->end_day = date('Y-m-d', time() + 24 * 3600);

        $this->basic_account = $this->view->identity->login;
        $this->cache_basic_account = md5($this->basic_account);
    }

    public function postDispatch()
    {
        $this->view->headTitle($this->view->title, 'PREPEND');
    }


    private function reconnect()
    {
        $urfa = new Urfa_Client();
        $urfa->restore_session($this->view->identity->utm5);

        return $urfa;
    }

    private function setTitle($title_name)
    {
        $this->view->title = $title_name;
    }

    /**
     * Экшен, обеспечивающий вывод информации о пользователе
     */
    public function indexAction()
    {
        $this->setTitle("Общая информация");

        $services = $tarrifs = $userData = null;

        //Проверяем наличие кэша
        //Если данные не присутствуют в кэше, то делаем запрос к urfe
        if (($services = $this->cache->load($this->cache_basic_account . '_services')) === false
            || ($tarrifs = $this->cache->load($this->cache_basic_account . '_tarrifs')) === false
            || ($userData = $this->cache->load($this->cache_basic_account)) === false
            || ($accounts = $this->cache->load($this->cache_basic_account . '_accounts')) === false
            || ($additional = $this->cache->load($this->cache_basic_account . '_additional')) === false
            || ($turbo = $this->cache->load($this->cache_basic_account . '_turbo')) === false
        ) {
            $urfa = $this->reconnect();

            //получаем информацию о сервисах и сохраняем в кэш
            if ($services = $urfa->getServices()) {
                $this->cache->save($services, $this->cache_basic_account . '_services');
            }
            if ($tarrifs = $urfa->getTarrifs()) {
                $this->cache->save($tarrifs, $this->cache_basic_account . '_tarrifs');
            }
            if ($userData = $urfa->getUserInfo()) {
                $this->cache->save($userData, $this->cache_basic_account);
            }
            if ($additional = $urfa->getAdditional()) {
                $this->cache->save($additional, $this->cache_basic_account . '_additional');
            }
            if ($accounts = $urfa->getAccountsInfo()) {
                $this->cache->save($accounts, $this->cache_basic_account . '_accounts');
            }
            if ($turbo = $urfa->getTurboMode()) {
                $this->cache->save($accounts, $this->cache_basic_account . '_turbo');
            }
            unset($urfa);
        }
        //Присваиваем данные переменным вида
        $this->view->services = $services;
        // Zend_Debug::dump($services);
        $this->view->tarrifs = $tarrifs;
        // Zend_Debug::dump($tarrifs);
        $this->view->userData = $userData;

        $this->view->additional = $additional;

        $this->view->accounts = $accounts;

        $this->view->turbo = $turbo;

        $this->view->cacheData = $this->cache->getMetadatas($this->cache_basic_account);

        // $this->view->CONF_MIN_LOCKED_IN_FUNDS = $this->config->urfa->CONF_MIN_LOCKED_IN_FUNDS;

        // $this->view->editform = new Billing_Form_UserEdit();
        //$this->view->editform->setAction('/user/edit');

    }


    /**
     * Изменение статуса интернета
     */
    public function changeStatusAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $int_status = $this->_getParam('int_status');
        $acc_id = $this->_getParam('aid');

        if (isset($int_status)) {
            if ($int_status == 1 || $int_status == 0) {

                $urfa = $this->reconnect();

                $urfa->changeStatus($acc_id, $int_status);
                $this->_helper->flashMessenger->addMessage(array('success' => 'Состояние изменено'));
                $this->cache->remove($this->cache_basic_account . '_accounts');
            }
        }

        $this->_helper->redirector('index', 'index', 'billing');
    }


    /**
     * Экшен, обеспечивающий вывод информации о платежах
     */
    public function paymentAction()
    {
        $this->setTitle('Платежи');

        $start_date = strtotime($this->_getParam('startDate', '-' . $this->config->payment->default_report_period));

        if ($start_date < strtotime('-' . $this->config->payment->max_report_period)) {
            $start_date = strtotime('-' . $this->config->payment->max_report_period);
        }

        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                //Проверяем наличие кэша
                //Если данные не присутствуют в кэше, то делаем запрос к urfe
                $cacheId = $this->cache_basic_account . '_payment' . DRG_Util::getCacheByDate($start_date, $end_date);
                if ((($payment = $this->cache->load($cacheId)) === false)) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();

                    //получаем информацию о платежах и сохраняем в кэш
                    if ($payment = $urfa->get_payments_report($start_date, $end_date)) {
                        function sortByOrder($a, $b)
                        {
                            return $b['date_of_payment_unix'] - $a['date_of_payment_unix'];
                        }

                        usort($payment, 'sortByOrder');
                        $this->cache->save($payment, $cacheId);
                    }
                    //уничтожаем объект Urfaphp_URFAClientUser5
                    unset($urfa);
                }

                $cacheId = $this->cache_basic_account . '_burntPayment';
                if (($burntPayment = $this->cache->load($cacheId)) === false) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();
                    //получаем информацию и сохраняем в кэш
                    if ($burntPayment = $urfa->getBurntPayment()) {
                        $this->cache->save($burntPayment, $cacheId);
                    }
                    //уничтожаем объект Urfaphp_URFAClientUser5
                    unset($urfa);
                }

                //Присваиваем данные переменным вида
                $this->view->userPaymentData = $payment;
                $this->view->burntPaymentData = $burntPayment;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }

    }

    /**
     * Экшен, обеспечивающий вывод информации о сервисе
     */
    public function serviceAction()
    {
        $this->setTitle('Информация об услуге');

        $service = null;
        $slink = $this->_getParam('slink', null);
        if (!$slink) {
            throw new Urfa_Exception('Не верная ссылка на услугу', 500);
        }

        //Проверяем наличие кэша
        //Если данные не присутствуют в кэше, то делаем запрос к urfe

        $cacheId = $this->cache_basic_account . '_service_' . $slink;
        if (($service = $this->cache->load($cacheId)) === false) {
            //Создаём подключение к urfe
            $urfa = $this->reconnect();
            //получаем информацию о пользователе и сохраняем в кэш
            if ($service = $urfa->getServiceInfo($slink)) {
                $this->cache->save($service, $cacheId);
            }
            //уничтожаем объект Urfaphp_URFAClientUser5
            unset($urfa);
        }
        //Присваиваем данные переменным вида
        $this->view->services = $service;
        //Zend_Debug::dump($service);

        $this->view->cacheData = $this->cache->getMetadatas($cacheId);

    }


    public function trafficAction()
    {
        $this->setTitle('Информация о трафике');

        $traffic = null;
        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        if ($start_date < strtotime('-' . $this->config->traffic->max_report_period)) {
            $start_date = strtotime('-' . $this->config->traffic->max_report_period);
        }

        $this->view->form = new Billing_Form_Traffic($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                $serviceType = $this->_getParam('serviceType', '2');
                switch ($serviceType) {
                    case 1:
                        $cacheId = $this->cache_basic_account . '_traffic_report_' . DRG_Util::getCacheByDate(
                                $start_date,
                                $end_date
                            );
                        if (($traffic = $this->cache->load($cacheId)) === false) {
                            $urfa = $this->reconnect();
                            if ($traffic = $urfa->get_traffic_report($start_date, $end_date)) {
                                $this->cache->save($traffic, $cacheId);
                            }
                            unset($urfa);
                        }
                        $this->_helper->viewRenderer('traffic');
                        break;
                    case 2:
                        $cacheId = $this->cache_basic_account . '_traffic_report_by_date_' . DRG_Util::getCacheByDate(
                                $start_date,
                                $end_date
                            );
                        if (($traffic = $this->cache->load($cacheId)) === false) {
                            $urfa = $this->reconnect();
                            if ($traffic = $urfa->get_traffic_report_by_date($start_date, $end_date)) {
                                $this->cache->save($traffic, $cacheId);
                            }
                            unset($urfa);
                        }
                        $this->_helper->viewRenderer('trafficdate');
                        break;
                    case 3:
                        $cacheId = $this->cache_basic_account . '_traffic_report_by_ip_' . DRG_Util::getCacheByDate(
                                $start_date,
                                $end_date
                            );
                        if (($traffic = $this->cache->load($cacheId)) === false) {
                            $urfa = $this->reconnect();
                            if ($traffic = $urfa->get_traffic_report_by_ip($start_date, $end_date)) {
                                $this->cache->save($traffic, $cacheId);
                            }
                            unset($urfa);
                        }
                        $this->_helper->viewRenderer('trafficip');
                        break;
                }
                $this->view->traffic = $traffic;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }
    }

    public function serviceReportAction()
    {
        $this->setTitle('Информация по услугам');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        if ($start_date < strtotime('-' . $this->config->services->max_report_period)) {
            $start_date = strtotime('-' . $this->config->services->max_report_period);
        }

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {

                $cacheId
                    =
                    $this->cache_basic_account . '_service_report_' . DRG_Util::getCacheByDate($start_date, $end_date);
                if (($service_report = $this->cache->load($cacheId)) === false) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();
                    //получаем информацию о пользователе и сохраняем в кэш
                    if ($service_report = $urfa->get_service_report($start_date, $end_date)) {
                        $this->cache->save($service_report, $cacheId);
                    }
                    //уничтожаем объект Urfaphp_URFAClientUser5
                    unset($urfa);
                }
                //Присваиваем данные переменным вида
                $this->view->service_report = $service_report;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }

    }

    public function editAction()
    {
        $this->setTitle('Редактирование профиля');

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
        }
        $message = null;
        $this->view->form = new Billing_Form_UserEdit();

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {

                $urfa = $this->reconnect();

                $messages = $data = $this->view->form->getValues();


                foreach ($messages as $name => $value) {
                    $message .= $name . ' ' . $value . '. ';
                }
                $urfa->sendMessage('Редактирование профиля', $message);
                //уничтожаем объект Urfaphp_URFAClientUser5
                unset($urfa);
                $this->_helper->flashMessenger->addMessage(array('success' => 'Сообщение отправлено'));
                $this->_helper->redirector('index', 'index', 'billing');
            }
        }

    }

    public function messagesAction()
    {
        $this->setTitle('Сообщения');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {

                $cacheId = $this->cache_basic_account . '_messages_' . DRG_Util::getCacheByDate($start_date, $end_date);
                if (($messages = $this->cache->load($cacheId)) === false) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();
                    //получаем информацию о пользователе и сохраняем в кэш
                    if ($messages = $urfa->getMessages($start_date, $end_date)) {
                        $this->cache->save($messages, $cacheId);
                    }
                    //уничтожаем объект Urfaphp_URFAClientUser5
                    unset($urfa);
                }
                //Присваиваем данные переменным вида
                $this->view->messages = $messages;
                //Zend_Debug::dump($messages);
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }

    }

    public function messageAction()
    {
        $this->setTitle('Сообщения');

        $id = $this->_getParam('id');
        $tag = $this->_getParam('tag');


        if (intval($id)) {

            if ($tag === 'newMail') {
                $this->cache->clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                    array('newMail')
                );
            }

            $cacheId = $this->cache_basic_account . '_message_' . $id;
            if (($message = $this->cache->load($cacheId)) === false) {
                //Создаём подключение к urfe
                $urfa = $this->reconnect();
                //получаем информацию о пользователе и сохраняем в кэш
                if ($message = $urfa->getMessage($id)) {
                    $this->cache->save($message, $cacheId);
                }
                //уничтожаем объект Urfaphp_URFAClientUser5
                unset($urfa);
            }
            //Присваиваем данные переменным вида
            $this->view->message = $message;
            // Zend_Debug::dump($message);
            $this->view->cacheData = $this->cache->getMetadatas($cacheId);
        }

    }

    public function newMessagesAction()
    {
        $this->setTitle('Новые сообщения');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        $cacheId = $this->cache_basic_account . '_new_messages_' . DRG_Util::getCacheByDate($start_date, $end_date);
        if (($new_messages = $this->cache->load($cacheId)) === false) {
            //Создаём подключение к urfe
            $urfa = $this->reconnect();
            //получаем информацию о пользователе и сохраняем в кэш
            if ($new_messages = $urfa->getNewMessages($start_date, $end_date)) {
                $this->cache->save($new_messages, $cacheId, array('newMail'));
            }
            //уничтожаем объект Urfaphp_URFAClientUser5
            unset($urfa);
        }

        //Присваиваем данные переменным вида
        $this->view->new_messages = $new_messages;

        $this->view->cacheData = $this->cache->getMetadatas($cacheId);

    }

    public function newMessageAction()
    {
        $this->setTitle('Новое сообщение');

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
        }
        $this->view->form = new Billing_Form_Message();
        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {

                $urfa = $this->reconnect();

                $message = $this->view->form->getValues();

                $urfa->sendMessage($message['subject'], $message['message']);

                $this->_helper->flashMessenger->addMessage(
                    array('success' => 'Сообщение отправлено')
                );
                $this->redirect('/user/sent-messages/');

                //уничтожаем объект Urfaphp_URFAClientUser5
                unset($urfa);
            }
        }

    }

    public function sentMessagesAction()
    {
        $this->setTitle('Отправленные сообщения');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        $cacheId = $this->cache_basic_account . '_sent_messages_' . DRG_Util::getCacheByDate($start_date, $end_date);
        if (($sent_messages = $this->cache->load($cacheId)) === false) {

            $urfa = $this->reconnect();

            //получаем информацию о пользователе и сохраняем в кэш
            if ($sent_messages = $urfa->getSentMessages($start_date, $end_date)) {
                $this->cache->save($sent_messages, $cacheId);
            }

            unset($urfa);
        }
        //Присваиваем данные переменным вида
        $this->view->sent_messages = $sent_messages;
        $this->view->cacheData = $this->cache->getMetadatas($cacheId);

    }

    public function changePasswordAction()
    {
        $this->setTitle('Редактировать пароль к личному кабинету');

        $this->_helper->viewRenderer('edit');

        $form = new Billing_Form_ChangePassword();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {

                $urfa = $this->reconnect();

                $data = $form->getValues();

                $result = $urfa->changePasswordForCabinet(
                    $data['old_password'],
                    $data['new_password'],
                    $data['new_password_repeat']
                );

                if ($result) {
                    $this->_helper->flashMessenger->addMessage(
                        array('success' => 'Пароль успешно изменён')
                    );
                    $this->_helper->redirector('index');
                } else {
                    $this->view->error = 'При изменении пароля произошла ошибка';
                }


            }
        }

        $this->view->form = $form;
    }

    public function changeServicePasswordAction()
    {
        $this->setTitle('Редактировать пароль для услуги');

        $slink_id = $this->_getParam('slink_id');
        $item_id = $this->_getParam('item_id');

        if (!isset($slink_id) || !isset($item_id)) {
            throw new Urfa_Exception('Не верно указаны параметры тарифа для изменения пароля', 500);
        }

        $this->_helper->viewRenderer('edit');
        $form = new Billing_Form_ChangePassword();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {

                $urfa = $this->reconnect();

                $data = $form->getValues();

                $result = $urfa->changePassword(
                    $slink_id,
                    $item_id,
                    $data['old_password'],
                    $data['new_password'],
                    $data['new_password_repeat']
                );
                if ($result) {
                    $this->_helper->flashMessenger->addMessage(
                        array('success' => 'Пароль успешно изменён')
                    );
                    $this->_helper->redirector('index');
                } else {
                    $this->view->error = 'При изменении пароля произошла ошибка';
                }
            }
        }

        $this->view->form = $form;

    }

    /**
     * Добавление обещанного платежа пользователю
     */
    public function promisePaymentAction()
    {
        $this->setTitle('Обещанный платёж');

        $aid = $this->_getParam('aid', 0);

        $urfa = $this->reconnect();

        $this->view->promiseCreditInfo = $urfa->getPromisePaymentInfo($aid);

        $form = new Billing_Form_Credit($this->view->promiseCreditInfo['value'], $this->view->promiseCreditInfo['flags']);

        if ($this->view->promiseCreditInfo['can_change'] && $this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $summa = $this->_getParam('amount');
                $urfa->addPromisePayment($aid, $summa);
                $this->cache->remove($this->cache_basic_account);
                $urfa->changeStatus($aid, 1);
                $this->_helper->redirector('promise-payment', 'index', 'billing');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Добравольная блокировка
     */
    public function blockAction()
    {
        $this->setTitle('Добровольная блокировка');

        $aid = $this->_getParam('aid', 0);

        $urfa = $this->reconnect();

        $this->view->blockInfo = $urfa->getBlockInfo($aid);

        $form = new Billing_Form_Block();
        $form_del = new Billing_Form_BlockDel();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost()) || $form_del->isValid($this->getRequest()->getPost())) {
                $startDate = strtotime($this->_getParam('startDate'));
                $endDate = strtotime($this->_getParam('endDate'));

                if ($startDate < time() + 3600) {
                    $startDate = time() + 3600;
                }

                $act = $this->_getParam('act');

                if ($act == 1) {

                    if ($startDate > $endDate) {
                        $result = -1;
                    } else {
                        $result = $urfa->setBlock($startDate, $endDate, $aid);
                    }
                    if ($result == 1) {
                        $this->_helper->flashMessenger->addMessage(
                            array('success' => 'Добровольная блокировка успешно установлена')
                        );
                    } elseif ($result == -1) {
                        $this->_helper->flashMessenger->addMessage(
                            array('danger' => 'Не верно заданы даты для установки добровольной блокировки')
                        );
                    } else {
                        $this->_helper->flashMessenger->addMessage(
                            array('danger' => 'При установке добровольной блокировки произошла ошибка')
                        );
                    }
                } elseif ($act == 2) {
                    $result = $urfa->delBlock($aid);
                    if ($result == 1) {
                        $this->_helper->flashMessenger->addMessage(
                            array('success' => 'Добровольная блокировка успешно снята')
                        );
                    } else {
                        $this->_helper->flashMessenger->addMessage(
                            array('danger' => 'При снятии добровольной блокировки произошла ошибка')
                        );
                    }
                }
                $this->redirect('/user/block/aid/' . $aid);
            }
        }

        $this->view->form = $form;
        $this->view->form_del = $form_del;
    }

    /**
     * Смена тарифа
     */
    public function changeTariffAction()
    {
        $this->setTitle('Смена тарифа');

        $aid = $this->_getParam('aid');
        $tlink_id = $this->_getParam('tlink_id');
        $this->view->next_tp = $this->_getParam('next_tp');

        if (!isset($aid) || !isset($tlink_id)) {

            if (($tarrifs = $this->cache->load($this->cache_basic_account . '_tarrifs')) === false) {
                $urfa = $this->reconnect();

                //получаем информацию о сервисах и сохраняем в кэш

                if ($tarrifs = $urfa->getTarrifs()) {
                    $this->cache->save($tarrifs, $this->cache_basic_account . '_tarrifs');
                }
                unset($urfa);
            }
            if (count($tarrifs) == 1) {
                foreach ($tarrifs as $account => $tarrif) {
                    if (count($tarrif) == 1) {
                        $aid = $tarrif[0]['aid'];
                        $tlink_id = $tarrif[0]['id'];
                    } else {
                        $this->_helper->flashMessenger->addMessage(
                            array('danger' => 'Не верно заданы параметры тарифа')
                        );
                        $this->redirect('/user/');
                    }
                }
            } else {
                $this->_helper->flashMessenger->addMessage(array('danger' => 'Не верно заданы параметры тарифа'));
                $this->redirect('/user/');
            }

        }

        $urfa = $this->reconnect();

        $this->view->tariffInfo = $urfa->getTariffInfo($aid, $tlink_id);
        $this->view->changeTariffInfo = $urfa->changeTariffInfo($aid, $tlink_id);

        if (isset($this->view->changeTariffInfo['tariff']) && is_array($this->view->changeTariffInfo['tariff'])) {

            $form = new Billing_Form_ChangeTariff($this->view->changeTariffInfo);


            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {


                    $result = $urfa->changeTariff($aid, $tlink_id, $form->getValue('next_tp'));

                    if ($result > 0) {
                        $this->cache->remove($this->cache_basic_account);
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Тариф успешно изменён'));
                    } else {
                        $this->_helper->flashMessenger->addMessage(
                            array('danger' => 'При изменении тарифа произошла ошибка')
                        );
                    }

                    $this->redirect('/user/');
                }

            }
            $this->view->form_error = $form->getErrors();
            $this->view->form = $form;
        }
    }


    /**
     * История изменения тарифов
     */
    public function tariffHistoryAction()
    {
        $this->setTitle('История смены тарифов');

        $cacheId = $this->cache_basic_account . '_tariff_history';
        if (($tariffHistory = $this->cache->load($cacheId)) === false) {

            $urfa = $this->reconnect();

            //получаем информацию о пользователе и сохраняем в кэш
            if ($tariffHistory = $urfa->gettariffHistory()) {
                $this->cache->save($tariffHistory, $cacheId);
            }
            unset($urfa);
        }
        //Присваиваем данные переменным вида
        $this->view->tariffHistory = $tariffHistory;
        $this->view->cacheData = $this->cache->getMetadatas($cacheId);
    }

    /**
     * Выставленные счета
     */
    public function invoicesAction()
    {
        $this->setTitle('Выставленные счета');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                //Проверяем наличие кэша
                //Если данные не присутствуют в кэше, то делаем запрос к urfe
                $cacheId = $this->cache_basic_account . '_invoices' . DRG_Util::getCacheByDate($start_date, $end_date);
                if ((($invoices = $this->cache->load($cacheId)) === false)) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();

                    //получаем информацию о выставленных счетах и сохраняем в кэш
                    if ($invoices = $urfa->getInvoices($start_date, $end_date)) {
                        $this->cache->save($invoices, $cacheId);
                    }

                    unset($urfa);
                }
                //Присваиваем данные переменным вида
                $this->view->invoices = $invoices;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }
    }

    public function invoiceDocumentAction()
    {

        $this->_helper->layout()->disableLayout();

        $id = $this->_getParam('id');

        if (!isset($id)) {
            throw new Urfa_Exception('Не верно указан счёта', 500);
        }

        //Проверяем наличие кэша
        //Если данные не присутствуют в кэше, то делаем запрос к urfe
        $cacheId = $this->cache_basic_account . '_invoiceDocument' . $id;
        if ((($invoiceDocument = $this->cache->load($cacheId)) === false)) {
            //Создаём подключение к urfe
            $urfa = $this->reconnect();

            //получаем информацию о выставленных счетах и сохраняем в кэш
            if ($invoiceDocument = $urfa->getInvoiceDocument($id)) {
                $this->cache->save($invoiceDocument, $cacheId);
            }

            unset($urfa);
        }
        //Присваиваем данные переменным вида
        $this->view->invoiceDocument = $invoiceDocument;

    }

    /**
     * Отчёт по блокировкам
     */
    public function blockingReportAction()
    {
        $this->setTitle('Отчёт по блокировкам');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                //Проверяем наличие кэша
                //Если данные не присутствуют в кэше, то делаем запрос к urfe
                $cacheId
                    = $this->cache_basic_account . '_blockingReport' . DRG_Util::getCacheByDate($start_date, $end_date);
                if ((($blockingReport = $this->cache->load($cacheId)) === false)) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();

                    //получаем информацию о выставленных счетах и сохраняем в кэш
                    if ($blockingReport = $urfa->getBlockingReport($start_date, $end_date)) {
                        $this->cache->save($blockingReport, $cacheId);
                    }
                    unset($urfa);
                }
                //Присваиваем данные переменным вида
                $this->view->blockingReport = $blockingReport;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }
    }

    /**
     * Отчёт по сессиям
     */
    public function dhsReportAction()
    {
        $this->setTitle('Отчёт по сессиям');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                //Проверяем наличие кэша
                //Если данные не присутствуют в кэше, то делаем запрос к urfe
                $cacheId = $this->cache_basic_account . '_DhsReport' . DRG_Util::getCacheByDate($start_date, $end_date);
                if ((($DHSReport = $this->cache->load($cacheId)) === false)) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();

                    //получаем информацию о выставленных счетах и сохраняем в кэш
                    if ($DHSReport = $urfa->getDHSReport($start_date, $end_date)) {
                        $this->cache->save($DHSReport, $cacheId);
                    }

                    unset($urfa);
                }
                //Присваиваем данные переменным вида
                $this->view->DHSReport = $DHSReport;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }
    }

    public function cardPaymentAction()
    {
        $this->setTitle('Предоплаченные карты доступа');

        $this->_helper->viewRenderer('edit');

        $urfa = $this->reconnect();
        $accounts = $urfa->getAccounts();

        $this->view->form = $form = new Billing_Form_Card($accounts);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $result = $urfa->cardPayment(
                    $form->getValue('account'),
                    $form->getValue('card'),
                    $form->getValue('pin')
                );
                if ($result['state'] == 0) {
                    $this->_helper->flashMessenger->addMessage(
                        array('danger' => 'При активации произошла ошибка. ' . $result['message'])
                    );
                } else {
                    $this->_helper->flashMessenger->addMessage(
                        array('success' => 'Карта активирована')
                    );
                    $this->cache->remove($this->cache_basic_account);
                }
                $this->redirect('/user/card-payment/');
            }
        }

    }

    public function paymentDocumentAction()
    {

        $this->setTitle('Платёжный документ');

        $this->_helper->viewRenderer('edit');

        $this->view->form = $form = new Billing_Form_Payment();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {

                $urfa = $this->reconnect();
                $text = $urfa->getInvoiceDocument(0, 27);
                $text = str_replace("@SUM@", $form->getValue('sum'), $text);

                $this->_helper->layout()->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                echo $text;
            }
        }

    }


    public function rentsoftAction()
    {

        $this->setTitle('Услуги по подписке');

        $this->_helper->viewRenderer->setNoRender(true);

        $urfa = $this->reconnect();
        $user = $urfa->getUserInfo();

        // URL of the current page.
        $rsReferrer = ($_SERVER['SERVER_PORT'] == 443 ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']
            . $_SERVER['REQUEST_URI'];

        $api = $this->config->urfaphp->host . ':' . $this->config->urfaphp->port;
        if (!$api) {
            // Detect API address if we can.
            $cand = array();
            $cand[] = $this->config->urfaphp->host;
            $cand[] = create_function('', 'return gethostbyname(php_uname("n"));'); // delay execution until usage
            $cand[] = $_SERVER['SERVER_ADDR'];


            list ($cand, $port) = array($cand, $this->config->rentsoft->nxt_v2_bind_port);
            foreach ($cand as $ip) {
                if (is_callable($ip)) {
                    $ip = $ip();
                }
                if ($ip && !preg_match('/^(0|192|127|10|172)\./s', $ip)) {
                    $api = "$ip:$port";
                }
            }
            if (!$api || !$port) {
                echo "Не удается определить адрес API ядра UTM5. <ul><li>Пожалуйста, укажите его вручную в файле billing.ini, переменная rentsoft.api_addr, в формате: \"хост:порт\". Порт должен быть доступен из интернета и вести на машину с ядром UTM5.</li><li>Не забудьте также указать директивы nxt_v2_bind_host и nxt_v2_bind_port в /netup/utm5/utm5.cfg на машине с ядром биллинга.</li></ul>";

                return;
            }
        }
        if ($user['is_juridical'] && !$this->config->rentsoft->allow_juridicals) {
            echo "Извините, подписка на ПО для юридических лиц недоступна.";
        } else {

            echo Urfa_Rentsoft::getIframe(
                @$_GET['rs_uri'],
                $rsReferrer,
                $this->config->rentsoft->ag_name,
                $user['basic_account'],
                $api,
                $this->config->rentsoft->secret,
                null,
                '880px'
            );
        }

    }


    /**
     * Экшен, обеспечивающий вывод информации о платежах
     */
    public function telephonyReportAction()
    {
        $this->setTitle('Отчет по телефонии');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                //Проверяем наличие кэша
                //Если данные не присутствуют в кэше, то делаем запрос к urfe
                $cacheId
                    =
                    $this->cache_basic_account . '_telephonyReport' . DRG_Util::getCacheByDate($start_date, $end_date);
                if ((($telephony = $this->cache->load($cacheId)) === false)) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();

                    //получаем информацию о платежах и сохраняем в кэш
                    if ($telephony = $urfa->getTelephonyReport($start_date, $end_date)) {
                        $this->cache->save($telephony, $cacheId);
                    }
                    //уничтожаем объект Urfaphp_URFAClientUser5
                    unset($urfa);
                }
                //Присваиваем данные переменным вида
                $this->view->telephony = $telephony;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }

    }

    /**
     * Возврат заблокированных средств
     * @since 5.2.1-009
     */
    public function repayAction()
    {
        $this->setTitle('Заблокированные средства');

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $account = $this->_getParam('account');

        if (isset($account) && preg_match("/^[0-9]+$/", $account)) {
            $urfa = $this->reconnect();
            $urfa->repay($account);
            $this->cache->remove($this->cache_basic_account . '_accounts');
        } else {
            $this->_helper->flashMessenger->addMessage(
                array('danger' => 'Не верно задан лицевой счёт')
            );
        }
        $this->redirect('/user/');

    }

    /**
     * Турборежим
     * @since 5.2.1-009
     */
    public function turboModeAction()
    {
        $this->setTitle('Турбо режим');

        $slink_id = $this->_getParam('slink_id');

        if (isset($slink_id)) {
            $slink_id = (int)$slink_id;
        } else {
            $this->_helper->flashMessenger->addMessage(
                array('danger' => 'Не верно указан ID услуги')
            );
            $this->redirect('/user/');
        }

        $urfa = $this->reconnect();
        $this->view->turboModeInfo = $urfa->getTurboModeInfo($slink_id);

        $this->view->form = new Billing_Form_TurboMode();

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                if ($urfa->setTurboMode($slink_id)) {
                    $this->_helper->flashMessenger->addMessage(
                        array('success' => 'Турбо режим установлен')
                    );
                } else {
                    $this->_helper->flashMessenger->addMessage(
                        array('danger' => 'При установке турбо режима произошла ошибка')
                    );
                }
                $this->redirect('/user/');
            }
        }

    }

    /**
     * Экшен, обеспечивающий вывод информации о прочих списаниях
     * @since 5.2.1-009
     */
    public function otherChargesReportAction()
    {
        $this->setTitle('Прочие списания');

        $start_date = strtotime($this->_getParam('startDate', $this->start_day));
        $end_date = strtotime($this->_getParam('endDate', $this->end_day));

        $this->view->form = new Billing_Form_ByDate($start_date, $end_date);

        if ($this->getRequest()->isPost()) {
            if ($this->view->form->isValid($this->getRequest()->getPost())) {
                //Проверяем наличие кэша
                //Если данные не присутствуют в кэше, то делаем запрос к urfe
                $cacheId = $this->cache_basic_account . '_otherChargesReport' . DRG_Util::getCacheByDate(
                        $start_date,
                        $end_date
                    );
                if ((($otherCharges = $this->cache->load($cacheId)) === false)) {
                    //Создаём подключение к urfe
                    $urfa = $this->reconnect();

                    //получаем информацию о платежах и сохраняем в кэш
                    if ($otherCharges = $urfa->getOtherChargesReport($start_date, $end_date)) {
                        $this->cache->save($otherCharges, $cacheId);
                    }
                }
                //Присваиваем данные переменным вида
                $this->view->otherCharges = $otherCharges;
                $this->view->cacheData = $this->cache->getMetadatas($cacheId);
            }
        }

    }

}