<?php

require __DIR__ . '/vendor/autoload.php';

function khipu_create_payment($receiver_id, $secret, $params) {

    $configuration = new Khipu\Configuration();
    $configuration->setSecret($secret);
    $configuration->setReceiverId($receiver_id);
    $configuration->setPlatform('opencart-khipu', '2.8.1');

    $client = new Khipu\ApiClient($configuration);
    $payments = new Khipu\Client\PaymentsApi($client);


    $createPaymentResponse = $payments->paymentsPost(
        $params['subject']
        , $params['currency_code']
        , $params['amount']
        , $params['transaction_id']
        , $params['custom']
        , $params['body']
        , $params['bank_id']
        , $params['return_url']
        , $params['cancel_url']
        , null
        , $params['notify_url']
        , '1.3'
        , null
        , null
        , null
        , $params['payer_email']
        , null
        , null
        , null
        , null
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
    $configuration->setPlatform('opencart-khipu', '2.8.1');

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
        $configuration->setPlatform('opencart-khipu', '2.8.1');

        $client = new Khipu\ApiClient($configuration);
        $payments = new Khipu\Client\PaymentsApi($client);

        return $payments->paymentsGet($params['notification_token']);
    }
	return 0;
}
