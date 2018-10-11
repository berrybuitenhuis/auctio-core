<?php

namespace AuctioCore\Api\Hexon\Entity\Custom;

use AuctioCore\Api\Base;
use AuctioCore\Api\BaseInterface;

class DateTime extends Base implements BaseInterface {

    public function populate($data) {
        // Get timestamp of data
        if (empty($data)) {
            return;
        } elseif ($data instanceof \DateTime) {
            $timestamp = $data->getTimestamp();
        } elseif (is_array($data)) {
            $date = new \DateTime($data['date']);
            $timestamp = $date->getTimestamp();
        } else {
            // Avoid invalid date-strings
            try {
                $date = new \DateTime($data);
                $timestamp = $date->getTimestamp();
            } catch (\Exception $e) {
                return;
            }
        }

        // Set new date-time object
        $this->date = new \DateTime();
        $this->date->setTimestamp($timestamp);
        return $this;
    }

    /**
     * Returns a JSON encoded string with current Entity.
     * We have filtered out the readOnly elements
     * @return string
     */
    public function encode(){
        // Return if empty
        if (!isset($this->date) || empty($this->date)) return null;

        // Set timezone to Europe/Amsterdam
        $this->date->setTimezone(new \DateTimeZone('Europe/Amsterdam'));

        // Return (ISO-8601 format)
        return $this->date->format(\DateTime::ATOM);
    }
}