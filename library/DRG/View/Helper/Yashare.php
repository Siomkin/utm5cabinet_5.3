<?php
/**
 * Helper for making Yandex share
 *
 * @author    Siomkin Alexandr <mail@mg7.by>
 * @link      http://www.jext.biz/
 * @copyright Copyright &copy; 2011-2012
 * @license   GNU General Public License, version 2:
 *            http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class DRG_View_Helper_Yashare extends Zend_View_Helper_Abstract
{

    /**
     * @param string $title
     * @param string $route
     * @param array  $params
     *
     * @return string
     */
    public function yashare(array $params = array())
    {
        $html = '<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div> ';
        return $html;
    }

}