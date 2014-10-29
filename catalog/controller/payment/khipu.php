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

	public function terminal() {
		$data['javascript'] = $this->get_terminal_javascript(html_entity_decode($_GET['data']));
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/khipu-terminal.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/khipu-terminal.tpl';
		} else {
			$this->template = 'default/template/payment/khipu-terminal.tpl';
		}
		$data['wait_message'] = $this->language->get('Estamos iniciando el terminal de pagos khipu, por favor espera unos minutos.<br>No cierres esta página, una vez que completes el pago serás redirigido automáticamente.');



        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');



		$this->response->setOutput( $this->load->view($this->template, $data));
	}

	public function process() {

		$json_string = khipu_create_payment($this->config->get('khipu_receiverid')
				, $this->config->get('khipu_secret')
				, $this->request->post
				, 'opencart-khipu-2.1');

		// We need the string json to use it with the khipu.js
		$response = json_decode($json_string);

		if (!$response) {
			// TODO ERROR //return $this->comm_error();
			error_log('no response from khipu');
			return;
		}

		$readyForTerminal = 'ready-for-terminal';

		if (!$response->$readyForTerminal) {
            $this->response->redirect($response->url);
			return;
		}
		$this->response->redirect($this->url->link('payment/khipu/terminal', 'data=' . urlencode($json_string), 'SSL'));
	}


	public function index() {
		$this->language->load('payment/khipu');

		$this->load->model('checkout/order');


		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$data['receiver_id'] = $this->config->get('khipu_receiverid');
			$data['subject'] = html_entity_decode($this->config->get('config_name') . ' Order #' . $this->session->data['order_id'], ENT_QUOTES, 'UTF-8');

			$body = '';
			foreach ($this->cart->getProducts() as $product) {
				$body .= $product['name'] . ' ' . $product['model'] . ' x ' . $product['quantity'] . ' ';
			}

			$total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);

			$data['amount'] = $total;
			$data['body'] = $body;
			$data['payer_email'] = $order_info['email'];
			$data['transaction_id'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['return_url'] = $this->url->link('checkout/success');
			$data['notify_url'] = $this->url->link('payment/khipu/callback', '', 'SSL');
			$data['cancel_url'] = $this->url->link('checkout/checkout', '', 'SSL');
			$data['picture_url'] = '';
			$data['custom'] = $this->session->data['order_id'];

			$banks = khipu_get_available_banks($data['receiver_id']
				, $this->config->get('khipu_secret')
				, 'opencart-khipu-2.1');
				
			$data['javascript'] = khipu_banks_javascript($banks);

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/khipu.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/khipu.tpl';
			} else {
				$this->template = 'default/template/payment/khipu.tpl';
			}

			$data['bank_selector_label'] = $this->language->get('Selecciona el banco para pagar');
			$data['button_confirm'] = $this->language->get('button_confirm');
			$data['action'] = $this->url->link('payment/khipu/process', '', 'SSL');
            		return $this->load->view($this->template, $data);
		}
	}

	public function callback() {
		if (isset($this->request->post['custom'])) {
			$order_id = $this->request->post['custom'];
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			if ($this->request->post['receiver_id'] != $this->config->get('khipu_receiverid')) {
				error_log("Received distinct receiver_id: " . $this->request->post['receiver_id'] . "\n");
				exit(0);
			}
			$ret = khipu_verify_payment_notification($this->config->get('khipu_receiverid')
                                , $this->config->get('khipu_secret')
                                , $this->request->post
                                , 'opencart-khipu-2.0');

			$response = $ret['response'];

			if (!$response) {
				error_log(curl_error($ret['info']));
				$this->log->write('KHIPU :: CURL failed ' . curl_error($ret['info']) . '(' . curl_errno($ret['info']) . ')');
			}
			if ($response == 'VERIFIED') {
				$order_status_id = $this->config->get('khipu_completed_status_id');
				$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
			} else {
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'));
			}
		} else {
			error_log("no order_info for order_id $order_id\n");
		}
	}
}

?>
