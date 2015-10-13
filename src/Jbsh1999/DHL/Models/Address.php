<?php

namespace Address;

class Address {

    private $_street_name;

    private $_street_number;

    private $_zip;              // Postal code. Max length = 5 for Germany, 8 for England, 10 for other country

    private $_city;

    private $_country_iso;

    public function setAddress($street_name, $street_number, $zip, $city, $country_iso)
    {
        if (strlen($street_name)>30) {
            $this->_errors[] = 'Length of street name could not be more than 30 symbols';
        } else {
            $this->_street_name = $street_name;
        }
        if (strlen($street_number)>20) {
            $this->_errors[] = 'Length of street number could not be more than 7 symbols';
        } else {
            $this->_street_number = $street_number;
        }
        $max_length = 10;
        if ($country_iso=='DE') {
            $max_length = 5;
        }
        if ($country_iso=='GB') {
            $max_length = 8;
        }
        if (strlen($zip)>$max_length) {
            $this->_errors[] = 'Length of zip could not be more than '.$max_length.' symbols';
        } else {
            $this->_zip = $zip;
        }
        if (strlen($city)>20) {
            $this->_errors[] = 'Length of street name could not be more than 20 symbols';
        } else {
            $this->_city = $city;
        }
        if (strlen($country_iso)>2) {
            $this->_errors[] = 'Country ISO code must be in ISO 3166-1 alpha-2 format';
        } else {
            $this->_country_iso = $country_iso;
        }

        return $this;
    }

} 