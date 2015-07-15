<?php

require_once('khipu_commons.php');

class ControllerPaymentKhipu extends Controller {

	protected function get_terminal_javascript($data) {
		return <<<EOD
<script>
	window.onload = function () {
		KhipuLib.onLoad({
			data: $data
    	})
	}
</script>
EOD;
	}


    function base64url_encode_compress($data) {
        return rtrim(strtr(base64_encode(gzcompress($data)), '+/', '-_'), '=');
    }

    function base64url_decode_uncompress($data) {
        return gzuncompress(base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)));
    }



	public function terminal() {
		$this->data['javascript'] = $this->get_terminal_javascript($this->base64url_decode_uncompress($_REQUEST['data']));
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/khipu-terminal.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/khipu-terminal.tpl';
		} else {
			$this->template = 'default/template/payment/khipu-terminal.tpl';
		}
		$this->data['wait_message'] = $this->language->get('Estamos iniciando el terminal de pagos khipu, por favor espera unos minutos.<br>No cierres esta página, una vez que completes el pago serás redirigido automáticamente.');
		$this->children = array('common/column_left'
		, 'common/column_right'
		, 'common/content_top'
		, 'common/content_bottom'
		, 'common/footer'
		, 'common/header'
		);
		$this->document->addScript('//cdnjs.cloudflare.com/ajax/libs/atmosphere/2.1.2/atmosphere.min.js');
		$this->document->addScript('//storage.googleapis.com/installer/khipu-1.1.jquery.js');

		$this->response->setOutput($this->render());
	}

	public function process() {
		$json_string = khipu_create_payment($this->config->get('khipu_receiverid')
				, $this->config->get('khipu_secret')
				, $this->request->post
				, 'opencart-khipu-1.5.6;;'.$this->config->get('config_url').';;'.$this->config->get('config_name'));

		// We need the string json to use it with the khipu.js
		$response = json_decode($json_string);

		if (!$response) {
			// TODO ERROR //return $this->comm_error();
			error_log('no response from khipu');
			return;
		}

		$readyForTerminal = 'ready-for-terminal';

		if (!$response->$readyForTerminal) {
			$this->redirect($response->url);
			return;
		}
		$this->redirect($this->url->link('payment/khipu/terminal', 'data=' . $this->base64url_encode_compress($json_string), 'SSL'));
	}


	protected function index() {
		$this->language->load('payment/khipu');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$this->data['receiver_id'] = $this->config->get('khipu_receiverid');
			$this->data['subject'] = html_entity_decode($this->config->get('config_name') . ' Order #' . $this->session->data['order_id'], ENT_QUOTES, 'UTF-8');

			$body = '';
			foreach ($this->cart->getProducts() as $product) {
				$body .= $product['name'] . ' ' . $product['model'] . ' x ' . $product['quantity'] . ' ';
			}

			$total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);

			$this->data['amount'] = $total;
			$this->data['body'] = $body;
			$this->data['payer_email'] = $order_info['email'];
			$this->data['transaction_id'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['return_url'] = $this->url->link('checkout/success');
			$this->data['notify_url'] = $this->url->link('payment/khipu/callback', '', 'SSL');
			$this->data['cancel_url'] = $this->url->link('checkout/checkout', '', 'SSL');
			$this->data['picture_url'] = '';
			$this->data['custom'] = $this->session->data['order_id'];

			$banks = khipu_get_available_banks($this->data['receiver_id']
				, $this->config->get('khipu_secret')
				, 'opencart-khipu-1.5.6;;'.$this->config->get('config_url').';;'.$this->config->get('config_name'));
				
			$this->data['javascript'] = khipu_banks_javascript($banks);

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/khipu.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/khipu.tpl';
			} else {
				$this->template = 'default/template/payment/khipu.tpl';
			}

			$this->data['bank_selector_label'] = $this->language->get('Selecciona el banco para pagar');
			$this->data['button_confirm'] = $this->language->get('button_confirm');
			$this->data['action'] = $this->url->link('payment/khipu/process', '', 'SSL');
			$this->render();
		}
	}


	public function callback() {
		$order_id = khipu_get_verified_order_id($this->request->post['api_version'], $this->config->get('khipu_receiverid'), $this->config->get('khipu_secret'), $this->request->post, $this->config->get('config_url'), $this->config->get('config_name'));
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if ($order_info) {
			if (!$order_info['order_status_id']) {
				$this->model_checkout_order->confirm($order_id, $this->config->get('khipu_completed_status_id'));
			} else {
				$this->model_checkout_order->update($order_id, $this->config->get('khipu_completed_status_id'));
			}
		} else {
			error_log("no order_info for order_id $order_id\n");
		}
	}
}

?>
