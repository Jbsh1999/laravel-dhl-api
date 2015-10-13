<?php

$shipper                                = array();
$shipper['Company']                     = array();
$shipper['Company']['Company']          = array();
$shipper['Company']['Company']['name1'] = $this->info['company_name'];

$shipper['Address']                                                = array();
$shipper['Address']['streetName']                                  = $this->info['street_name'];
$shipper['Address']['streetNumber']                                = $this->info['street_number'];
$shipper['Address']['Zip']                                         = array();
$shipper['Address']['Zip'][ strtolower( $this->info['country'] ) ] = $this->info['zip'];
$shipper['Address']['city']                                        = $this->info['city'];

$shipper['Address']['Origin'] = array( 'countryISOCode' => 'DE' );

$shipper['Communication']                  = array();
$shipper['Communication']['email']         = $this->info['email'];
$shipper['Communication']['phone']         = $this->info['phone'];
$shipper['Communication']['internet']      = $this->info['internet'];
$shipper['Communication']['contactPerson'] = $this->info['contact_person'];

$receiver = array();

$receiver['Company']                        = array();
$receiver['Company']['Person']              = array();
$receiver['Company']['Person']['firstname'] = $customer_details['first_name'];
$receiver['Company']['Person']['lastname']  = $customer_details['last_name'];

$receiver['Address']                                                      = array();
$receiver['Address']['streetName']                                        = $customer_details['street_name'];
$receiver['Address']['streetNumber']                                      = $customer_details['street_number'];
$receiver['Address']['Zip']                                               = array();
$receiver['Address']['Zip'][ strtolower( $customer_details['country'] ) ] = $customer_details['zip'];
$receiver['Address']['city']                                              = $customer_details['city'];
$receiver['Communication']                                                = array();
$receiver['Communication']['contactPerson']                               = $customer_details['phone'];


$receiver['Address']['Origin'] = array( 'countryISOCode' => 'RU' );


$dhl = DHLAPI::createInternationalShipment($shipper, $receiver, true);

$response = $dhl->createInternationalShipment($customer_details);

if($response !== false) {

    var_dump($response);

    echo '---------------------------------------------------------------------';

    $shipment_id = $response['shipment_number'];

    $response = $dhl->deleteShipment($shipment_id);
    if($response !== false) {

        var_dump($response);
    } else {

        var_dump($dhl->errors);

    }

} else {

    var_dump($dhl->errors);

}