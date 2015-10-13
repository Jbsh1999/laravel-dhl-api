<?php

namespace ExportDocPosition;

class ExportDocPosition
{
    private $_description;              // string max length = 40

    private $_country_code_origin;      // string ISO 3166-1 alpha-2 format

    private $_commodity_code;           // optional string max length = 30 (http://www.wcoomd.org/en/topics/nomenclature/instrument-and-tools/hs_nomenclature_2012/hs_nomenclature_table_2012.aspx)

    private $_amount;                   // integer min = 1 max length = 22

    private $_net_weight_in_kg;         // decimal max length = 22

    private $_gross_weight_in_kg;       // decimal max length = 22

    private $_customs_value;            // decimal max length = 11

    private $_customs_currency;         // string ISO 4217 format

    private $_errors;


    public function setExportDocLine($description, $country_code_origin='DE', $commodity_code='-', $amount, $net_weight_in_kg, $gross_weight_in_kg, $customs_value, $customs_currency='EUR')
    {
        if (strlen($description)>40) {
            $this->_errors[] = 'Length of description could not be more than 40 symbols';
        } else {
            $this->_description = $description;
        }
        if (strlen($country_code_origin)!=2) {
            $this->_errors[] = 'Country ISO code must be in ISO 3166-1 alpha-2 format';
        } else {
            $this->_country_code_origin = $country_code_origin;
        }
        if (strlen($commodity_code)>30) {
            $this->_errors[] = 'Length of commodity code could not be more than 30 symbols';
        } else {
            $this->_commodity_code = $commodity_code;
        }
        if (!is_int($amount)) {
            $this->_errors[] = 'Amount must be integer';
        } else {
            $this->_amount = $amount;
        }
        if (!preg_match('/^\d+\.\d+$/',$net_weight_in_kg)) {
            $this->_errors[] = 'Net weight must be decimal';
        } else {
            if (strlen($net_weight_in_kg)>22) {
                $this->_errors[] = 'Length of net weight could not be more than 22 symbols';
            } else {
                $this->_net_weight_in_kg = $net_weight_in_kg;
            }
        }
        if (!preg_match('/^\d+\.\d+$/',$gross_weight_in_kg)) {
            $this->_errors[] = 'Gross weight must be decimal';
        } else {
            if (strlen($gross_weight_in_kg)>22) {
                $this->_errors[] = 'Length of gross weight could not be more than 22 symbols';
            } else {
                $this->_gross_weight_in_kg = $gross_weight_in_kg;
            }
        }
        if (!preg_match('/^\d+\.\d+$/',$customs_value)) {
            $this->_errors[] = 'Customs value must be decimal';
        } else {
            if (strlen($customs_value)>11) {
                $this->_errors[] = 'Length of customs value could not be more than 11 symbols';
            } else {
                $this->_customs_value = $customs_value;
            }
        }
        if (strlen($customs_currency)!=3) {
            $this->_errors[] = 'Country ISO code must be in ISO 4217 format';
        } else {
            $this->_customs_currency = $customs_currency;
        }

        return $this;
    }

}