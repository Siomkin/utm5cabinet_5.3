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
    public static function blockedStatus($block_val)
    {
        switch($block_val){
            case 4294967295:
            case -1: //none
                return "Разблокирован";
                break;
            case 1: //system
                return "Системная блокировка";
                break;
            case 2: //admin
                return "Админская блокировка";
                break;
            case 3: //user
                return "Пользовательская блокировка";
                break;
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