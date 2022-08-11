<?php

require_once('khipu_commons.php');

class ControllerExtensionPaymentKhipu extends Controller {


    function khipu_error($exception)
    {
        $this->template = 'extension/payment/khipuerror';
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['exception'] = $exception;
        $this->response->setOutput($this->load->view($this->template, $data));
    }

    public function process() {

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['receiver_id'] = $this->config->get('payment_khipu_receiverid');
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
        $data['notify_url'] = $this->url->link('extension/payment/khipu/callback', '', 'SSL');
        $data['cancel_url'] = $this->url->link('checkout/checkout', '', 'SSL');
        $data['picture_url'] = '';
        $data['custom'] = $this->session->data['order_id'];
        $data['bank_id'] = $this->request->post['bank_id'];
        $data['currency_code'] = $order_info['currency_code'];


        $this->template = 'extension/payment/khipu';


        try {
            $createPaymentResponse = khipu_create_payment($this->config->get('payment_khipu_receiverid')
                , $this->config->get('payment_khipu_secret')
                , $data);

        } catch (\Khipu\ApiException $e) {
            $this->khipu_error($e->getResponseObject());
            return;
        }
        $this->response->redirect($createPaymentResponse->getSimplifiedTransferUrl());
    }


	public function index() {
		$this->language->load('extension/payment/khipu');

		$this->load->model('checkout/order');


		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
            $data['valid_currency'] = $order_info['currency_code'] == 'CLP';
            $data['invalid_currency_message'] = $this->language->get('invalid_currency_message');
			$data['receiver_id'] = $this->config->get('payment_khipu_receiverid');
			$data['subject'] = html_entity_decode($this->config->get('config_name') . ' Order #' . $this->session->data['order_id'], ENT_QUOTES, 'UTF-8');

			$body = '';
			foreach ($this->cart->getProducts() as $product) {
				$body .= $product['name'] . ' ' . $product['model'] . ' x ' . $product['quantity'] . ' ';
			}

           	$data['button_confirm'] = $this->language->get('button_confirm');
			$data['action'] = $this->url->link('extension/payment/khipu/process', '', 'SSL');
            return $this->load->view('extension/payment/khipu', $data);
		}
	}

	public function callback() {
		$payment = khipu_get_payment($this->request->post['api_version'], $this->config->get('payment_khipu_receiverid'), $this->config->get('payment_khipu_secret'), $this->request->post);


        if(! $payment instanceof \Khipu\Model\PaymentsResponse) {
            error_log("invalid response\n");
            return;
        }


        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($payment->getCustom());
        $total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
        if($payment->getReceiverId() == $this->config->get('payment_khipu_receiverid')
            && $payment->getStatus() == 'done'
            && $total == $payment->getAmount()
            && $order_info['currency_code'] == $payment->getCurrency()
        ) {
            $this->model_checkout_order->addOrderHistory($payment->getCustom(), $this->config->get('payment_khipu_completed_status_id'));
        } else {
            error_log("invalid response\n");
        }
	}
}

?>
