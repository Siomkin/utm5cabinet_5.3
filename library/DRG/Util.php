<?php

class DRG_Util
{
    /**
     * Получаем имя для кэш файла
     * @param string|array $data
     */
    public static function getCacheName($name, $data = NULL)
    {

        if (is_array($data)) {
            sort($data);
            foreach ($data as $value) {
                $name .= $value;
            }
        } else {
            $name .= $data;
        }
        return md5($name);
    }

    /**
     * Имя для кеш файла двух дат
     * @static
     *
     * @param int    $start_date <p>
     *                           The timestamp which is used as a base for cache dates.
     * </p>
     * @param int    $end_date   <p>
     *                           The timestamp which is used as a base for cache dates.
     * </p>
     * @param string $time       [optional]<p>
     *                           The string to parse. Before PHP 5.0.0, microseconds weren't allowed in
     *                           the time, since PHP 5.0.0 they are allowed but ignored.
     * </p>
     *
     * @return string
     */
    public static function getCacheByDate($start_date, $end_date, $time = 'dmY')
    {
        $data = '';
        if ($start_date) {
            $data .= date($time, $start_date);
        }
        if ($end_date) {
            $data .= date($time, $end_date);
        }
        return $data;
    }


}