<?php

namespace ExportDoc;

class ExportDoc
{
    private $_invoice_type;                     // optional string enumeration (mandatory for BPI) possible values is 'proforma' or 'commercial'

    private $_invoice_date;                     // string in format yyyy-mm-dd format

    private $_invoice_number;                   // optional string max length = 30

    private $_export_type;                      // optional string enumeration (mandatory for BPI) possible values is '0'='other', '1'='gift', '2'='sample', '3'='documents', '4'='goods return'

    private $_export_type_description;          // optional string (mandatory for export_type = '0') max length = 30

    private $_commodity_code;                   // optional string max length = 30 (http://www.wcoomd.org/en/topics/nomenclature/instrument-and-tools/hs_nomenclature_2012/hs_nomenclature_table_2012.aspx)

    private $_terms_of_trade;                   // string in incoterms format (https://en.wikipedia.org/wiki/Incoterms) length must be 3

    private $_amount;                           // integer min = 1 max length = 22

    private $_description;                      // string

    private $_country_code_origin;              // string ISO 3166-1 alpha-2 format

    private $_additional_fee;                   // optional decimal max length = 11

    private $_customs_value;                    // decimal max length = 11

    private $_customs_currency;                 // string ISO 4217 format

    private $_permit_number;                    // optional string max length = 30

    private $_attestation_number;               // optional string max length = 30

    private $_with_electronic_export_ntfctn;    // optional boolean

    private $_export_doc_position;              // optional array of object's

    private function validateDate($value, $format = 'yyyy-mm-dd')
    {
        if (strlen($value) >= 6 && strlen($format) == 10) {
            // find separator. Remove all other characters from $format
            $separator_only = str_replace(array('m','d','y'),'', $format);
            $separator = $separator_only[0]; // separator is first character
            if ($separator && strlen($separator_only) == 2) {
                // make regex
                $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
                $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
                $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
                $regexp = str_replace($separator, "\\" . $separator, $regexp);
                if ($regexp != $value && preg_match('/'.$regexp.'\z/', $value)) {
                    // check date
                    $arr=explode($separator,$value);
                    $day=$arr[0];
                    $month=$arr[1];
                    $year=$arr[2];
                    if (checkdate($month, $day, $year)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function setExportDoc($invoice_type='proforma', $invoice_date, $invoice_number='-', $export_type='0', $export_type_description='export goods', $commodity_code='-', $terms_of_trade='CIP', $amount, $description,
                                 $country_code_origin='DE', $additional_fee=0.00, $customs_value, $customs_currency='EUR', $permit_number='-', $attestation_number='-', $with_electronic_export_ntfctn=false, $export_doc_position)
    {
        if ($invoice_type!='proforma' || $invoice_type!='commercial') {
            $this->_errors[] = 'Invoice type mismatch';
        } else {
            $this->_invoice_type = $invoice_type;
        }
        if (!$this->validateDate($invoice_date)) {
            $this->_errors[] = 'Invoice date is wrong';
        } else {
            $this->_invoice_date = $invoice_date;
        }
        if (strlen($invoice_number)>30) {
            $this->_errors[] = 'Length of invoice number could not be more than 30 symbols';
        } else {
            $this->_invoice_number = $invoice_number;
        }
        if ($export_type!='0' || $export_type!='1' || $export_type!='2' || $export_type!='3' || $export_type!='4') {
            $this->_errors[] = 'Export type mismatch';
        } else {
            $this->_export_type = $export_type;
        }
        if ($export_type=='0') {
            if (strlen($export_type_description)>30) {
                $this->_errors[] = 'Length of export type description could not be more than 30 symbols';
            } else {
                $this->_export_type_description = $export_type_description;
            }
        }
        if (strlen($commodity_code)>30) {
            $this->_errors[] = 'Length of commodity code could not be more than 30 symbols';
        } else {
            $this->_commodity_code = $commodity_code;
        }
        if (strlen($terms_of_trade)!=3) {
            $this->_errors[] = 'Length of terms of trade must be 3 symbols';
        } else {
            $this->_terms_of_trade = $terms_of_trade;
        }
        if (!is_int($amount)) {
            $this->_errors[] = 'Amount must be integer';
        } else {
            $this->_amount = $amount;
        }
        $this->_description = $description;
        if (strlen($country_code_origin)!=2) {
            $this->_errors[] = 'Country ISO code must be in ISO 3166-1 alpha-2 format';
        } else {
            $this->_country_code_origin = $country_code_origin;
        }
        if (!preg_match('/^\d+\.\d+$/',$additional_fee)) {
            $this->_errors[] = 'Additional_fee must be decimal';
        } else {
            if (strlen($customs_value)>11) {
                $this->_errors[] = 'Length of additional fee could not be more than 11 symbols';
            } else {
                $this->_additional_fee = $additional_fee;
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
        if (strlen($permit_number)>30) {
            $this->_errors[] = 'Length of permit number could not be more than 30 symbols';
        } else {
            $this->_permit_number = $permit_number;
        }
        if (strlen($attestation_number)>30) {
            $this->_errors[] = 'Length of attestation number could not be more than 30 symbols';
        } else {
            $this->_attestation_number = $attestation_number;
        }
        if (!is_bool($with_electronic_export_ntfctn)) {
            $this->_errors[] = 'With_electronic_export_ntfctn must be boolean';
        } else {
            $this->_with_electronic_export_ntfctn = $with_electronic_export_ntfctn;
        }
        foreach ($export_doc_position as $export_doc_position_line) {
            $row = new ExportDocPosition;
            $this->_export_doc_position[] = $row->setExportDocLine( $export_doc_position_line['description'],$export_doc_position_line['country_code_origin'],$export_doc_position_line['ommodity_code'],
                                                                    $export_doc_position_line['amount'],
                                                                    $export_doc_position_line['net_weight_in_kg'],$export_doc_position_line['gross_weight_in_kg'],
                                                                    $export_doc_position_line['customs_value'],$export_doc_position_line['customs_currency']);
        }

        return $this;
    }

}