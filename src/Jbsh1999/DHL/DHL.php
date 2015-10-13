<?php namespace Jbsh1999\DHL;

class DHLBusinessShipment {

    private $sender;

    private $receiver;

    private $shipments;

    public $errors;

    protected $mode;

    private $client;


    public function __construct($mode) {
        if ($mode) {
            $this->credentials['user']          = config('dhl.live.user');
            $this->credentials['signature']     = config('dhl.live.signature');
            $this->credentials['ekp']           = config('dhl.live.ekp');
            $this->credentials['api_user']      = config('dhl.live.api_user');
            $this->credentials['api_password']  = config('dhl.live.api_password');
            $this->credentials['api_url']       = config('dhl.live.api_url');
            $this->credentials['location']      = config('dhl.live.location');
            $this->credentials['log']           = config('dhl.live.log');
        } else {
            $this->credentials['user']          = config('dhl.test.user');
            $this->credentials['signature']     = config('dhl.test.signature');
            $this->credentials['ekp']           = config('dhl.test.ekp');
            $this->credentials['api_user']      = config('dhl.test.api_user');
            $this->credentials['api_password']  = config('dhl.test.api_password');
            $this->credentials['api_url']       = config('dhl.test.api_url');
            $this->credentials['location']      = config('dhl.test.location');
            $this->credentials['log']           = config('dhl.test.log');
        }
    }

    private function buildAuthHeader() {
        $auth_params = array(
            'user'          => $this->credentials['user'],          // Required. (string max.20)
            'signature'     => $this->credentials['signature'],     // Required. (string max.100)
            //'accountNumber' => NULL,                              // Optional. DHL account number (14 digits). Account number. Field is currently obsolete and should be left empty. (string 14)
            'type'          => 0                                    // Required. Authentification mode Should always be zero. (integer)
        );
        return new SoapHeader( 'http://dhl.de/webservice/cisbase', 'Authentification', $auth_params );
    }

    private function buildClient() {

        $header = $this->buildAuthHeader();

        $location = $this->credentials['location'];
        
        $this->log( $location );

        $auth_params = array(
            'login'    => $this->credentials['api_user'],
            'password' => $this->credentials['api_password'],
            'location' => $location,
            'trace'    => 1

        );

        $this->log( $auth_params );

        $this->client = new SoapClient( $this->credentials['api_url'], $auth_params );

        $this->client->__setSoapHeaders( $header );

        $this->log( $this->client );


    }

    function createInternationalShipment( $sender, $receiver, $shipments, $type ) {

        $this->buildClient();

        $request = array();

        // Version
        $request['Version'] = array(
            'majorRelease'  => '1',                                     // Required. (string max.2)
            'minorRelease'  => '0',                                     // Required. (string max.2)
            //'build'         => ''                                     // Optional. Optional build id to be addressed. (string max.5)
        );

        $i = 1;
        $total_weight = 0;
        $total_value = 0;
        foreach ($shipments as $shipment) {

            // Order
            $request['ShipmentOrder'] = array();

            $request['ShipmentOrder']['SequenceNumber'] = strval($i);   // Required. Free field to to tag multiple shipment orders individually by client.
                                                                        // Essential for later mapping of response data returned by webservice upon createShipment operation.
                                                                        // Allows client to assign the shipment information of the response to the correct shipment order of the request. (string max.30)

            // Shipment
            $s                 = array();
            $s['ProductCode']  = 'BPI';                                 // Required. Means Bisness Pack International
            $s['ShipmentDate'] = date( 'Y-m-d' );                       // Required. Date of shipment should be close to current date and must not be in the past. Iso format required: yyyy-mm-dd. (string 8)


            //$s['DeclaredValueOfGoods']          = 0;                  // Optional. (float max.22)
            //$s['DeclaredValueOfGoodsCurrency']  = 'EUR';              // Optional, required if DeclaredValueOfGoods defined (string 3)


            $s['EKP']          = $this->credentials['ekp'];             // Required. (string 10)

            $s['Attendance']              = array();
            $s['Attendance']['partnerID'] = '02';                       // Required. Last 2 digits of your DHL contract (string 2)

            //$s['CustomerReference']  = '';                            // Optional. A reference number that the client can assign for better association purposes. Appears on shipment label. (string )
            //$s['Description']  = '';                                  // Optional. A description text that the client can assign. Does not appear on shipment label. (string )
            //$s['DeliveryRemarks']  = '';                              // Optional. Delivery remarks. Do not appear on shipment label.

            $s['ShipmentItem']               = array();
            $s['ShipmentItem']['WeightInKG'] = $shipment['weight'];     // Required. Weight in kg (decimal max 22)
            //$s['ShipmentItem']['LengthInCM'] = $shipment['length'];   // Optional. Length in cm (integer max 22)
            //$s['ShipmentItem']['WidthInCM']  = $shipment['width'];    // Optional. Width in cm (integer max 22)
            //$s['ShipmentItem']['HeightInCM'] = $shipment['height'];   // Optional. Height in cm (integer max 22)
            $s['ShipmentItem']['PackageType'] = 'PK';                   // Required.

            $s['Service']                                                                   = array();
            $s['Service']['ServiceGroupBusinessPackInternational']                          = array(); // Service group for Business Pack International.  Child elements: Economy, Premium, Seapacket, CoilWithoutHelp, Endorsement, AmountInternational
            $s['Service']['ServiceGroupBusinessPackInternational']['Economy']               = 'true';
            $s['Service']['ServiceGroupBusinessPackInternational']['Premium']               = 'false';
            //$s['Service']['ServiceGroupBusinessPackInternational']['Seapacket']             = 'false';
            //$s['Service']['ServiceGroupBusinessPackInternational']['CoilWithoutHelp']       = 'false';
            //$s['Service']['ServiceGroupBusinessPackInternational']['Endorsement']           = array();
            //$s['Service']['ServiceGroupBusinessPackInternational']['Endorsement']['Ident']  = '';
            //$s['Service']['ServiceGroupBusinessPackInternational']['Endorsement']['Days']   = 10;
            //$s['Service']['ServiceGroupBusinessPackInternational']['AmountInternational']   = 'false';
            if ($type=='premium') {
                $s['Service']['ServiceGroupBusinessPackInternational']['Economy'] = 'false';
                $s['Service']['ServiceGroupBusinessPackInternational']['Premium'] = 'true';
            }

            $total_weight+= $shipment['weight'];
            $total_value+= $shipment['value'];
        }


        $request['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;

        $request['ShipmentOrder']['Shipment']['Shipper'] = $sender;

        $request['ShipmentOrder']['Shipment']['Receiver'] = $receiver;

        $request['ShipmentOrder']['Shipment']['ExportDocument']                            = array();
        $request['ShipmentOrder']['Shipment']['ExportDocument']['InvoiceType']             = 'proforma';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['InvoiceDate']             = date( 'Y-m-d' );
        $request['ShipmentOrder']['Shipment']['ExportDocument']['InvoiceNumber']           = '------';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportType']              = '1';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportTypeDescription']   = 'Sonstiges';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['CommodityCode']           = '------';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['TermsOfTrade']            = 'CIP';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['Amount']                  = $total_value;
        $request['ShipmentOrder']['Shipment']['ExportDocument']['Description']             = '------';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['CountryCodeOrigin']       = 'DE';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['AdditionalFee']           = '0.00';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['CustomsValue']            = $total_value;
        $request['ShipmentOrder']['Shipment']['ExportDocument']['CustomsCurrency']         = 'EUR';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['PermitNumber']            = '------';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']                       = array();
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['Description']        = 'Clothes';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['CountryCodeOrigin']  = 'DE';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['CommodityCode']      = '123456';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['Amount']             = '1';
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['NetWeightInKG']      = $total_weight;
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['GrossWeightInKG']    = $total_weight;
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['CustomsValue']       = $total_value;
        $request['ShipmentOrder']['Shipment']['ExportDocument']['ExportDocPosition']['CustomsCurrency']    = 'EUR';

        $this->log($request);

        $response = $this->client->CreateShipmentDD( $request );

        $this->log($response);

        if ( is_soap_fault( $response ) || $response->status->StatusCode != 0 ) {

            if ( is_soap_fault( $response ) ) {

                $this->errors[] = 'soap fault';
                $this->errors[] = $response->faultstring;

            } else {

                $this->errors[] = 'response->status->StatusCode = '.$response->status->StatusCode;
                $this->errors[] = $response->status->StatusMessage;

                if ($response->CreationState->StatusCode != 0 ) {
                    $this->errors[] = 'response->CreationState->StatusCode = '.$response->CreationState->StatusCode;
                    $this->errors[] = $response->CreationState->StatusMessage;
                }

            }

            return false;

        } else {

            $r                    = array();
            $r['shipment_number'] = (String) $response->CreationState->ShipmentNumber->shipmentNumber;
            $r['piece_number']    = (String) $response->CreationState->PieceInformation->PieceNumber->licensePlate;
            $r['label_url']       = (String) $response->CreationState->Labelurl;

            return $r;
        }

    }



}