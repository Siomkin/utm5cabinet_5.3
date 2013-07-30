<?php
class DRG_Urfautils
{
    /**
     * Получаем статус блокировки
     * @static
     *
     * @param $id
     *
     * @return string
     */
    public static function blockedStatus($id)
    {

        switch ($id) {
            case 0:
                return "Разблокирован";
                break;
            case 16:
                return "Системная блокировка";
                break;
            case 256:
            case 272:
                return "Админская блокировка";
                break;
            default:
                return "Блокировка";
        }
    }

    /**
     * Возвращает статус пользователя
     * @static
     *
     * @param $id
     *
     * @return string
     */
    public static function intStatus($id)
    {
        switch ($id) {
            case 1:
                return "Включён";
                break;
            case 0:
                return "Выключен";
                break;
            default:
                return "неизвестно";
        }
    }

    /**
     * Преобразуем расчетный период в необходимый нам вид
     * @static
     *
     * @param $date
     *
     * @return string
     */
    public static function serviceDate($date)
    {
        $DateArray = explode("-", $date);
        $beginDate = date("d.m.Y H:i", strtotime($DateArray [0]));
        $endDate = date("d.m.Y H:i", strtotime($DateArray [1]));
        return $beginDate . " - " . $endDate;
    }
}