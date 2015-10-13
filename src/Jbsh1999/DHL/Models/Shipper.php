<?php

namespace Shipper;

class Shipper {

    private $_name;

    private $_surname;

    private $_company;

    private $_address;

    private $_communication;

    private $_errors;

    public function setShipper($name, $surname, $company, $address, $communication)
    {
        if (strlen($name)>30) {
            $this->_errors[] = 'Length of shipper name could not be more than 50 symbols';
        } else {
            $this->_name = $name;
        }
        if (strlen($surname)>30) {
            $this->_errors[] = 'Length of shipper surname could not be more than 50 symbols';
        } else {
            $this->_surname = $surname;
        }
        if (strlen($company)>30) {
            $this->_errors[] = 'Length of shipper company name could not be more than 50 symbols';
        } else {
            $this->_company = $company;
        }
        $this->_address = $address;
        $this->_communication = $communication;

        return $this;
    }

}

