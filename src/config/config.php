<?php

return array(
    'live' => [
        'user'          => '<YOUR LOGIN AT https://www.dhl-geschaeftskundenportal.de>',
        'signature'     => '<YOUR PASSWORD AT https://www.dhl-geschaeftskundenportal.de>',
        'ekp'           => '<YOUR_EKP>',                                                        // Your DHL Customer number. (Can be found at https://www.dhl-geschaeftskundenportal.de)
        'api_user'      => '<YOUR AppId AT https://entwickler.dhl.de>',                         // To generate App go to https://entwickler.dhl.de/home, then navigate to "Release & operation" (3-rd tab), then press button "Create new application"
        'api_password'  => '<TOKEN OF YOUR App>',
        'api_url'       => 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/1.0/geschaeftskundenversand-api-1.0.wsdl',
        'location'      => 'https://cig.dhl.de/services/production/soap',
        'log'           => true
    ],

    'test' => [
        'user'          => 'geschaeftskunden_api',
        'signature'     => 'Dhl_ep_test1',
        'ekp'           => '5000000000',
        'api_user'      => '<YOUR DeveloperId AT https://entwickler.dhl.de>',
        'api_password'  => '<YOUR PASSWORD AT https://entwickler.dhl.de>',
        'api_url'       => 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/1.0/geschaeftskundenversand-api-1.0.wsdl',
        'location'      => 'https://cig.dhl.de/services/sandbox/soap',
        'log'            => true
    ]

);

