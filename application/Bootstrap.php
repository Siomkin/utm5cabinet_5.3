<?php

/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initViewHelpers()
    {
        $this->bootstrapView();
        $view = $this->getResource('view');
        $view->addHelperPath(
            'ZendX/JQuery/View/Helper/',
            'ZendX_JQuery_View_Helper'
        );
        $view->addHelperPath('DRG/View/Helper/', 'DRG_View_Helper');
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv(
            'Content-Type',
            'text/html;charset=utf-8'
        );
        $view->headMeta()->appendName('description', 'UTM Cabinet');
        $view->headMeta()->appendName('keywords', 'UTM Cabinet');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('UTM Cabinet');
    }

    protected function _initNavigation()
    {
        $this->bootstrapView();

        $auth = Zend_Auth::getInstance();

        $view = $this->getResource('view');

        if (!$auth->hasIdentity()) {
            $view->identity = FALSE;
        } else {
            $view->identity = $auth->getIdentity();
        }
    }

    protected function _initTranslater()
    {
        $translator = new Zend_Translate(
            array('adapter' => 'array',
                'content' => APPLICATION_PATH . '/../resources/languages',
                'locale' => 'ru_RU',
                'scan' => Zend_Translate::LOCALE_DIRECTORY));
        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }

    protected function _initAppAutoload()
    {
        $loader = new Zend_Loader_Autoloader_Resource(
            array('basePath' => APPLICATION_PATH . '/modules/default',
                'namespace' => 'Default'));
        $loader->addResourceType('form', 'forms', 'Form')
            ->addResourceType('model', 'models', 'Model')
            ->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable');
        $autoloader = new Zend_Application_Module_Autoloader(
            array('namespace' => 'Default',
                'basePath' => dirname(__FILE__)));

        $loader = new Zend_Loader_Autoloader_Resource(
            array('basePath' => APPLICATION_PATH . '/modules/billing',
                'namespace' => 'Billing'));
        $loader->addResourceType('form', 'forms', 'Form')
            ->addResourceType('model', 'models', 'Model')
            ->addResourceType('dbtable', 'models/DbTable', 'Model_DbTable');
        $autoloader = new Zend_Application_Module_Autoloader(
            array('namespace' => 'Billing',
                'basePath' => dirname(__FILE__)));


        $frontController = Zend_Controller_Front::getInstance();
        $frontController->setControllerDirectory(
            array('default' => APPLICATION_PATH . '/modules/default/controllers',
                'stat' => APPLICATION_PATH . '/modules/stat/controllers',
                'users' => APPLICATION_PATH . '/modules/users/controllers')
        );
    }

    protected function _initZFDebug()
    {
        if (defined('APPLICATION_ENV') && APPLICATION_ENV != 'production') {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');
            $options = array(
                'plugins' => array('Variables',
                    'File' => array('base_path' => APPLICATION_PATH . '/../'), 'Html',
                    'Memory', 'Time', 'Registry', 'Exception'));
            # Instantiate the database adapter and setup the plugin.
            # Alternatively just add the plugin like above and rely on the autodiscovery feature.
            if ($this->hasPluginResource('db')) {
                $this->bootstrap('db');
                $db = $this->getPluginResource('db')->getDbAdapter();
                $options['plugins']['Database']['adapter'] = $db;
            }
            # Setup the cache plugin
            if ($this->hasPluginResource('cache')) {
                $this->bootstrap('cache');
                $cache = $this->getPluginResource('cache')->getDbAdapter();
                $options['plugins']['Cache']['backend'] = $cache->getBackend();
            }
            $debug = new ZFDebug_Controller_Plugin_Debug($options);
            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
            $frontController->registerPlugin($debug);
        }
    }

}

