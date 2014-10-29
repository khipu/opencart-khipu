<?php

require_once('lib-khipu/src/Khipu.php');

function khipu_create_payment($receiver_id, $secret, $params, $agent) {
	$Khipu = new Khipu();
	$Khipu->authenticate($receiver_id, $secret);
	$Khipu->setAgent($agent);
	$create_url_service = $Khipu->loadService('CreatePaymentURL');
	$create_url_service->setParameter('subject', $params['subject']);
	$create_url_service->setParameter('body', $params['body']);
	$create_url_service->setParameter('amount', $params['amount']);
	$create_url_service->setParameter('transaction_id', $params['transaction_id']);
	$create_url_service->setParameter('custom', $params['custom']);
	$create_url_service->setParameter('payer_email', $params['payer_email']);;
	$create_url_service->setParameter('notify_url', $params['notify_url']);
	$create_url_service->setParameter('bank_id', $params['bank_id']);
	$create_url_service->setParameter('return_url', $params['return_url']);
	return  $create_url_service->createUrl();
}


function khipu_banks_javascript($banks) {
	$javascript = <<<EOD
<script>
(function ($) {
                var bankRootSelect = $('#root-bank')
                var bankOptions = []
                var selectedRootBankId = 0
                var selectedBankId = 0
                bankRootSelect.attr("disabled", "disabled");

                function updateBankOptions(rootId, bankId) {
                        if (rootId) {
                                $('#root-bank').val(rootId)
                        }
EOD;
                foreach ($banks->banks as $bank) {
                        if (!$bank->parent) {
                                $javascript .= "bankRootSelect.append('<option value=\"$bank->id\">$bank->name</option>');\n";
                                $javascript .= "bankOptions['$bank->id'] = [];\n";
                                $javascript .= "bankOptions['$bank->id'].push('<option value=\"$bank->id\">$bank->type</option>')\n";
                        } else {
                                $javascript .= "bankOptions['$bank->parent'].push('<option value=\"$bank->id\">$bank->type</option>');\n";
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
        $(document).ready(function () {
                updateBankOptions(selectedRootBankId, selectedBankId);
                bankRootSelect.removeAttr("disabled");
        });
})(jQuery);
</script>
EOD;
	return $javascript;
}

function khipu_get_available_banks($receiver_id, $secret, $agent) {
	$Khipu = new Khipu();
	$Khipu->authenticate($receiver_id, $secret);
	$Khipu->setAgent($agent);
	$service = $Khipu->loadService('ReceiverBanks');
	return json_decode($service->consult());
}

function khipu_verify_payment_notification($receiver_id, $secret, $params, $agent) {
	$Khipu = new Khipu();
	$Khipu->authenticate($receiver_id, $secret);
	$Khipu->setAgent($agent);
	$service = $Khipu->loadService('VerifyPaymentNotification');
	$service->setParameter('return_url', $params['return_url']);
	$service->setParameter('api_version', $params['api_version']);
	$service->setParameter('receiver_id', $params['receiver_id']);
	$service->setParameter('notification_id', $params['notification_id']);
	$service->setParameter('subject', $params['subject']);
	$service->setParameter('amount', $params['amount']);
	$service->setParameter('currency', $params['currency']);
	$service->setParameter('custom', $params['custom']);
	$service->setParameter('transaction_id', $params['transaction_id']);
	$service->setParameter('payer_email', $params['payer_email']);
	$service->setParameter('notification_signature', $params['notification_signature']);

	return $service->verify();
}
