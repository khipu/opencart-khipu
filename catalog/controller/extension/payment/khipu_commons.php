<?php

require __DIR__ . '/vendor/autoload.php';

function khipu_create_payment($receiver_id, $secret, $params) {

    $configuration = new Khipu\Configuration();
    $configuration->setSecret($secret);
    $configuration->setReceiverId($receiver_id);
    $configuration->setPlatform('opencart-khipu', '3.0.2');

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

    if(array_key_exists('bank_id', $params)){
        $options['bank_id'] = $params['bank_id'];
    }

    $createPaymentResponse = $payments->paymentsPost(
        $params['subject']
        , $params['currency_code']
        , $params['amount']
        , $options
    );
    return $createPaymentResponse;

}


function khipu_banks_javascript($banks) {
	$javascript = <<<EOD
<script>
(function ($) {
                var bankRootSelect = $('#root-bank');
                var bankOptions = [];
                var selectedRootBankId = 0;
                var selectedBankId = 0;
                bankRootSelect.attr("disabled", "disabled");

                function updateBankOptions(rootId, bankId) {
                        if (rootId) {
                                $('#root-bank').val(rootId);
                        }
EOD;
                foreach ($banks as $bank) {
                        if (!$bank->getParent()) {
                                $javascript .= "bankRootSelect.append('<option value=\"" . $bank->getBankId() . "\">" . $bank->getName() . "</option>');\n";
                                $javascript .= "bankOptions['" . $bank->getBankId() . "'] = [];\n";
                                $javascript .= "bankOptions['" . $bank->getBankId() . "'].push('<option value=\"" . $bank->getBankId() . "\">" . $bank->getType() . "</option>');\n";
                        } else {
                                $javascript .= "bankOptions['" . $bank->getParent() . "'].push('<option value=\"" . $bank->getBankId() . "\">" . $bank->getType() . "</option>');\n";
                        }
                }
                $javascript .= <<<EOD
                        var idx = $('#root-bank :selected').val();
                        $('#bank-id').empty();
                        var options = bankOptions[idx];
                        for (var i = 0; i < options.length; i++) {
                                $('#bank-id').append(options[i]);
                        }
                        if (options.length > 1) {
                                $('#root-bank').addClass('form-control-left');
                                $('#bank-id').show();
                        } else {
                                $('#root-bank').removeClass('form-control-left');
                        $('#bank-id').hide();
                }
                if (bankId) {
                        $('#bank-id').val(bankId);
                }
                $('#bank-id').change();
        }

        $('#root-bank').change(function () {
                updateBankOptions();
        });

        updateBankOptions(selectedRootBankId, selectedBankId);
        bankRootSelect.removeAttr("disabled");

})(jQuery);
</script>
EOD;
	return $javascript;
}

function khipu_get_available_banks($receiver_id, $secret) {

    $configuration = new Khipu\Configuration();
    $configuration->setSecret($secret);
    $configuration->setReceiverId($receiver_id);
    $configuration->setPlatform('opencart-khipu', '3.0.2');

    $client = new Khipu\ApiClient($configuration);
    $banks = new Khipu\Client\BanksApi($client);

    $banksResponse = $banks->banksGet();
	return $banksResponse->getBanks();
}

function khipu_get_payment($api_version, $receiver_id, $secret, $params) {
	if ($api_version == '1.3') {
        $configuration = new Khipu\Configuration();
        $configuration->setSecret($secret);
        $configuration->setReceiverId($receiver_id);
        $configuration->setPlatform('opencart-khipu', '3.0.2');

        $client = new Khipu\ApiClient($configuration);
        $payments = new Khipu\Client\PaymentsApi($client);

        return $payments->paymentsGet($params['notification_token']);
    }
	return 0;
}
