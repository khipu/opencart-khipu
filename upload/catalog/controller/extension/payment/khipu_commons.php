<?php

require __DIR__ . '/vendor/autoload.php';

function khipu_create_payment($receiver_id, $secret, $params) {

    $configuration = new Khipu\Configuration();
    $configuration->setSecret($secret);
    $configuration->setReceiverId($receiver_id);
    $configuration->setPlatform('opencart-khipu', '4.1.0');

    $client = new Khipu\ApiClient($configuration);
    $payments = new Khipu\Client\PaymentsApi($client);

    $options = array(
          'transaction_id' => $params['transaction_id']
        , 'custom' => $params['custom']
        , 'body' => $params['body']
        , 'return_url' => $params['return_url']
        , 'cancel_url' => $params['cancel_url']
        , 'notify_url' => $params['notify_url']
        , 'notify_api_version' => '1.3'
        , 'payer_email' => $params['payer_email']
    );

    $createPaymentResponse = $payments->paymentsPost(
        $params['subject']
        , $params['currency_code']
        , $params['amount']
        , $options
    );
    return $createPaymentResponse;

}


function khipu_get_payment($api_version, $receiver_id, $secret, $params) {
	if ($api_version == '1.3') {
        $configuration = new Khipu\Configuration();
        $configuration->setSecret($secret);
        $configuration->setReceiverId($receiver_id);
        $configuration->setPlatform('opencart-khipu', '4.1.0');

        $client = new Khipu\ApiClient($configuration);
        $payments = new Khipu\Client\PaymentsApi($client);

        return $payments->paymentsGet($params['notification_token']);
    }
	return 0;
}
