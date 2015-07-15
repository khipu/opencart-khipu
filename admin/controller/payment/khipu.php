<?php
class ControllerPaymentKhipu extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/khipu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('khipu', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
	

		$this->data['entry_receiverid'] = $this->language->get('entry_receiverid');
		$this->data['entry_secret'] = $this->language->get('entry_secret');
		$this->data['entry_completed_status'] = $this->language->get('entry_completed_status');



		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['receiverid'])) {
			$this->data['error_receiverid'] = $this->error['receiverid'];
		} else {
			$this->data['error_receiverid'] = '';
		}

                if (isset($this->error['secret'])) {
                        $this->data['error_secret'] = $this->error['secret'];
                } else {
                        $this->data['error_secret'] = '';
                }


                if (isset($this->request->post['khipu_completed_status_id'])) {
                        $this->data['khipu_completed_status_id'] = $this->request->post['khipu_completed_status_id'];
                } else {
                        $this->data['khipu_completed_status_id'] = $this->config->get('khipu_completed_status_id');
                }

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/khipu', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('payment/khipu', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['khipu_receiverid'])) {
			$this->data['khipu_receiverid'] = $this->request->post['khipu_receiverid'];
		} else {
			$this->data['khipu_receiverid'] = $this->config->get('khipu_receiverid');
		}

                if (isset($this->request->post['khipu_secret'])) {
                        $this->data['khipu_secret'] = $this->request->post['khipu_secret'];
                } else {
                        $this->data['khipu_secret'] = $this->config->get('khipu_secret');
                }

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['khipu_geo_zone_id'])) {
			$this->data['khipu_geo_zone_id'] = $this->request->post['khipu_geo_zone_id'];
		} else {
			$this->data['khipu_geo_zone_id'] = $this->config->get('khipu_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['khipu_status'])) {
			$this->data['khipu_status'] = $this->request->post['khipu_status'];
		} else {
			$this->data['khipu_status'] = $this->config->get('khipu_status');
		}
		
		if (isset($this->request->post['khipu_sort_order'])) {
			$this->data['khipu_sort_order'] = $this->request->post['khipu_sort_order'];
		} else {
			$this->data['khipu_sort_order'] = $this->config->get('khipu_sort_order');
		}

		$this->template = 'payment/khipu.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
        if(!extension_loaded('curl')) {
            $this->error['warning'] = $this->language->get('curl_not_found');
        }
		if (!$this->user->hasPermission('modify', 'payment/khipu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['khipu_receiverid']) {
			$this->error['receiverid'] = $this->language->get('error_receiverid');
		}

                if (!$this->request->post['khipu_secret']) {
                        $this->error['secret'] = $this->language->get('error_secret');
                }

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>
