<?php
try {
// Define path to application directory
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
    set_include_path(
        implode(
            PATH_SEPARATOR, array(
                                 realpath(APPLICATION_PATH . '/../library'),
                                 get_include_path(),
                            )
        )
    );

    /** Zend_Application */
    require_once 'Zend/Application.php';

// Create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );

    if (!file_exists(APPLICATION_PATH . '/configs/billing.ini')) {
        throw new Zend_Exception('Не найден файл конфикурации urfa ' . APPLICATION_PATH . '/configs/billing.ini');
    }

    $application->bootstrap()->run();


} catch (Zend_Exception $exception) {
    echo "<html><body> <h3>Ошибка конфигурации приложения. Попробуйте зайти позже.</h3>";
    if (defined('APPLICATION_ENV') && APPLICATION_ENV != 'production') {
        echo $exception->getMessage();
    }
    echo "</body></html>";
}