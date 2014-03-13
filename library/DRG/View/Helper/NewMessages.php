<?php
/**
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_NewMessages extends Zend_View_Helper_Abstract
{
    public function newMessages($basic_account)
    {
        //за 3 месяца
        $start_date = strtotime(date('Y-m-d', time() - 90 * 24 * 3600));
        $end_date = strtotime(date('Y-m-d', time() + 24 * 3600));

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/billing.ini', 'app');
        //Инициализируем кэш
        //папка для хранения кэша
        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/' . $config->cache->cache_dir);
        //время жизни (сек), сериализация и логирование
        $frontendOptions = array('lifetime'                => $config->cache->lifetime,
                                 'debug_header'            => TRUE,
                                 'logger'                  => TRUE,
                                 'automatic_serialization' => TRUE);
        //метод храниния кэша. Определяет вторая переменная
        //Из наиболее используемых File, APC, возможен memcached, но там нужны дополнительные параметры
        //читайте документацию по Zend_Cache
        $cache = Zend_Cache::factory('Core', $config->cache->backend, $frontendOptions, $backendOptions);

        $cacheId = md5($basic_account . '_new_messages_count_' . DRG_Util::getCacheByDate($start_date, $end_date));
        if (($new_messages = $cache->load($cacheId)) === FALSE) {
            //Создаём подключение к urfe
            try {
                $urfa = new Urfa_Client();
                $urfa->restore_session($this->view->identity->utm5);
                //получаем информацию о пользователе и сохраняем в кэш
                if ($new_messages = $urfa->getNewMessages($start_date, $end_date)) {
                    $cache->save($new_messages, $cacheId, array('newMail'));
                }else{
                    $cache->save(array(), $cacheId, array('newMail'));
                }
                //уничтожаем объект Urfaphp_URFAClientUser5
                unset($urfa);
            }
            catch (Exception $ex) {

            }

        }
        $output = '';

        if (count($new_messages)>0) {
            $output .= '<span class="badge">' . count($new_messages) . '</span>';
        }
        return $output;
    }
}