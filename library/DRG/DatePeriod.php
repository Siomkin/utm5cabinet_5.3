<?php
/**
 * Class for making different periods of date without time.
 *
 * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
 */
class DRG_DatePeriod extends Zend_Date
{
    /**
     * Unix timestamp for date without time.
     *
     * @var int
     */
    private $_timestamp;

    /**
     * Year.
     *
     * @var int
     */
    private $_year;

    /**
     * Month.
     *
     * @var int
     */
    private $_month;

    /**
     * Day.
     *
     * @var int
     */
    private $_day;

    /**
     * Number of seconds for one day.
     */
    const DAY_SECONDS = 86400;

    /**
     * Constructor.
     *
     * @param string $date   String containing a US English date format (used in function strtotime).
     *
     * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
     */
    public function __construct($date = NULL)
    {
        // Using today


        if ($date == NULL) {
            $this->_timestamp = Zend_Date::now('ru')->getTimestamp();
        } else {
            $this->_timestamp = strtotime($date);
        }

        $this->_day = date('j', $this->_timestamp);
        $this->_month = date('n', $this->_timestamp);
        $this->_year = date('Y', $this->_timestamp);
    }

    /**
     * Calculates start and end date of week.
     * Returns array with two values: from date and to date.
     *
     * @param string $format  Format of the date (used in function date).
     *
     * @return array      Array with two keys: from and to. Example:
     *         array(
     * 'from' => ...
     * 'to'  => ...
     * )
     * @author DRG
     */
    public function getWeek($format)
    {
        $dayOfWeek = date('N', $this->_timestamp);
        return array(
            'from' => date($format, $this->_timestamp - ($dayOfWeek - 1) * self::DAY_SECONDS),
            'to'   => date($format, $this->_timestamp + (7 - $dayOfWeek) * self::DAY_SECONDS)
        );
    }

    /**
     * Calculates start and end date of month.
     * Return array with two values: from date and to date.
     *
     * @param string $format  Format of the date (used in function date).
     *
     * @return array      Array with two keys: from and to. Example:
     *         array(
     * 'from' => ...
     * 'to'  => ...
     * )
     * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
     */
    public function getMonth($format)
    {
        $monthDays = date('t', $this->_timestamp);

        return array('from' => date($format, strtotime($this->_year . '-' . $this->_month . '-01')),
                     'to'   => date($format, strtotime($this->_year . '-' . $this->_month . '-' . $monthDays)));
    }

    /**
     * Calculates start and end date of current quarter.
     * Return array with two values: from date and to date.
     *
     * @param string $format  Format of the date (used in function date).
     *
     * @return array      Array with two keys: from and to. Example:
     *         array(
     * 'from' => ...
     * 'to'  => ...
     * )
     * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
     */
    public function getCurrentQuarter($format)
    {
        $quarter = ( int )($this->_month / 4) + 1;

        $fromMonth = ($quarter - 1) * 3 + 1;
        $toMonth = $quarter * 3;

        // Number of days in last quarter month.


        $daysInToMonth = date('t', strtotime($this->_year . '-' . $toMonth . '-01'));

        return array('from' => date($format, strtotime($this->_year . '-' . $fromMonth . '-01')),
                     'to'   => date($format, strtotime($this->_year . '-' . $toMonth . '-' . $daysInToMonth)));
    }

    /**
     * Calculates start and end date of previous quarter.
     * Return array with two values: from date and to date.
     *
     * @param string $format  Format of the date (used in function date).
     *
     * @return array      Array with two keys: from and to. Example:
     *         array(
     * 'from' => ...
     * 'to'  => ...
     * )
     * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
     */
    public function getPrevQuarter($format)
    {
        $quarter = ( int )($this->_month / 4) + 1;

        if ($quarter == 1) {
            $quarter = 4;
            $year = $this->_year - 1;
        } else {
            $quarter -= 1;
            $year = $this->_year;
        }

        $fromMonth = ($quarter - 1) * 3 + 1;
        $toMonth = $quarter * 3;

        // Number of days in last quarter month.


        $daysInToMonth = date('t', strtotime($year . '-' . $toMonth . '-01'));

        return array('from' => date($format, strtotime($year . '-' . $fromMonth . '-01')),
                     'to'   => date($format, strtotime($year . '-' . $toMonth . '-' . $daysInToMonth)));
    }

    /**
     * Calculates start and end date of current calendar year to date.
     * Return array with two values: from date and to date.
     *
     * @param string $format  Format of the date (used in function date).
     *
     * @return array      Array with two keys: from and to. Example:
     *         array(
     * 'from' => ...
     * 'to'  => ...
     * )
     * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
     */
    public function getYearToDate($format)
    {
        return array('from' => date($format, strtotime($this->_year . '-01-01')),
                     'to'   => date($format, $this->_timestamp));
    }

    /**
     * Calculates start and end date of the last calendar year.
     * Return array with two values: from date and to date.
     *
     * @param string $format  Format of the date (used in function date).
     *
     * @return array      Array with two keys: from and to. Example:
     *         array(
     * 'from' => ...
     * 'to'  => ...
     * )
     * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
     */
    public function getLastYear($format)
    {
        return array('from' => date($format, strtotime($this->_year - 1 . '-01-01')),
                     'to'   => date($format, strtotime($this->_year - 1 . '-12-31')));
    }

    /**
     * Calculates start and end date of last calendar year to date.
     * Return array with two values: from date and to date.
     *
     * @param string $format  Format of the date (used in function date).
     *
     * @return array      Array with two keys: from and to. Example:
     *         array(
     * 'from' => ...
     * 'to'  => ...
     * )
     * @author Anton Vasilyev <anton.vasilyev@gtmdevelopments.com>
     */
    public function getTwelveMonths($format)
    {
        $fromMonth = (12 + ($this->_month - 11)) % 12 == 0 ? 1 : (12 + ($this->_month - 11)) % 12;

        if ($fromMonth > 1) {
            $year = $this->_year - 1;
        } else {
            $year = $this->_year;
        }

        // Number of days in last quarter month.


        $daysInToMonth = date('t', strtotime($this->_year . '-' . $this->_month . '-01'));

        return array('from' => date($format, strtotime($year . '-' . $fromMonth . '-01')),
                     'to'   => date($format, $this->_timestamp));
    }
}