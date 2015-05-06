<?php

require_once('khipu_commons.php');

class ControllerPaymentKhipuManual extends Controller {

	public function process() {
		$json_string = khipu_create_payment($this->config->get('khipu_manual_receiverid')
					, $this->config->get('khipu_manual_secret')
					, $this->request->post
					, 'opencart-khipu-manual-2.5;;'.$this->config->get('config_url').';;'.$this->config->get('config_name'));
		$response = json_decode($json_string);

		if (!$response) {
			error_log('no response from khipu');
			return;
		}
		$url = 'manual-url';
		$this->response->redirect($response->$url);
	}


	public function index() {
		$this->language->load('payment/khipu_manual');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$data['receiver_id'] = $this->config->get('khipu_manual_receiverid');
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
			$data['notify_url'] = $this->url->link('payment/khipu_manual/callback', '', 'SSL');
			$data['cancel_url'] = $this->url->link('checkout/checkout', '', 'SSL');
			$data['picture_url'] = '';
			$data['custom'] = $this->session->data['order_id'];

            $banks = khipu_get_available_banks($data['receiver_id']
                , $this->config->get('khipu_manual_secret')
                , 'opencart-khipu-manual-2.5;;'.$this->config->get('config_url').';;'.$this->config->get('config_name'));

			$data['javascript'] = khipu_banks_javascript($banks);

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/khipu_manual.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/khipu_manual.tpl';
			} else {
				$this->template = 'default/template/payment/khipu_manual.tpl';
			}

			$data['bank_selector_label'] = $this->language->get('Selecciona el banco para pagar');
			$data['button_confirm'] = $this->language->get('button_confirm');
			$data['action'] = $this->url->link('payment/khipu_manual/process', '', 'SSL');
			return $this->load->view($this->template, $data);
		}
	}

        public function callback() {
                $order_id = khipu_get_verified_order_id($this->request->post['api_version'], $this->config->get('khipu_manual_receiverid'), $this->config->get('khipu_manual_secret'), $this->request->post, $this->config->get('config_url'), $this->config->get('config_name'));
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if ($order_info) {
                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('khipu_completed_status_id'));
                } else {
                        error_log("no order_info for order_id $order_id\n");
                }
        }
}

?>
