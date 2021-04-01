<?php

namespace AuctioCore\Api\Adcurve\Entity\Custom;

use AuctioCore\Api\Base;
use AuctioCore\Api\BaseInterface;
use DateTime;
use DateTimeZone;
use Exception;

class Date extends Base implements BaseInterface {

    public function populate($data) {
        // Get timestamp of data
        if (empty($data)) {
            return;
        } elseif ($data instanceof DateTime) {
            $timestamp = $data->getTimestamp();
        } elseif (is_array($data)) {
            $date = new DateTime($data['date']);
            $timestamp = $date->getTimestamp();
        } else {
            // Avoid invalid date-strings
            try {
                $date = new DateTime($data);
                $timestamp = $date->getTimestamp();
            } catch (Exception $e) {
                return;
            }
        }

        // Set new date-time object
        $this->date = new DateTime();
        $this->date->setTimestamp($timestamp);
        return $this->date;
    }

    /**
     * Returns a JSON encoded string with current Entity.
     * We have filtered out the readOnly elements
     * @param bool $allowNull
     * @return string|null
     */
    public function encode($allowNull = true): ?string
    {
        // Return if empty
        if (!isset($this->date) || empty($this->date)) return null;

        // Set timezone to Europe/Amsterdam
        $this->date->setTimezone(new DateTimeZone('Europe/Amsterdam'));

        // Return
        return $this->date->format("Y-m-d");
    }
}