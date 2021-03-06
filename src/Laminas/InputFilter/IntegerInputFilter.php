<?php

namespace AuctioCore\Laminas\InputFilter;

use Laminas\InputFilter\InputFilter;

class IntegerInputFilter
{

    /**
     * Get InputFilter for a Integer-type field
     *
     * @param string $name
     * @param bool $allow_negative_values
     * @param bool $required
     * @return void|InputFilter
     */
    public static function getFilter(string $name, $allow_negative_values = false, $required = false)
    {
        if ($name == null) {
            return;
        } else {
            if ($allow_negative_values === true) {
                $filter = [
                    'name' => $name,
                    'required' => $required,
                    'validators' => [
                        [
                            'name' => 'Regex',
                            'options' => [
                                'pattern' => '/^[-]?\d*$/',
                            ],
                        ],
                    ],
                ];
            } else {
                $filter = [
                    'name' => $name,
                    'required' => $required,
                    'validators' => [
                        ['name' => 'Digits'],
                    ],
                ];
            }

            $inputFilter = new InputFilter();
            $inputFilter->add($filter, $name);
            return $inputFilter;
        }
    }

}