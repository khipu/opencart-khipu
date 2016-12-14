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
            $this->response->redirect($this->url->link('extension/payment/khipu', 'token=' . $this->session->data['token'], 'SSL'));
		}

        $data['text_edit'] = $this->language->get('text_edit');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');


		$data['entry_receiverid'] = $this->language->get('entry_receiverid');
		$data['entry_secret'] = $this->language->get('entry_secret');
		$data['entry_completed_status'] = $this->language->get('entry_completed_status');



		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

 		if (isset($this->error['receiverid'])) {
			$data['error_receiverid'] = $this->error['receiverid'];
		} else {
			$data['error_receiverid'] = '';
		}

                if (isset($this->error['secret'])) {
                        $data['error_secret'] = $this->error['secret'];
                } else {
                        $data['error_secret'] = '';
                }


                if (isset($this->request->post['khipu_completed_status_id'])) {
                        $data['khipu_completed_status_id'] = $this->request->post['khipu_completed_status_id'];
                } else {
                        $data['khipu_completed_status_id'] = $this->config->get('khipu_completed_status_id');
                }

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/khipu', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = $this->url->link('payment/khipu', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['khipu_receiverid'])) {
			$data['khipu_receiverid'] = $this->request->post['khipu_receiverid'];
		} else {
			$data['khipu_receiverid'] = $this->config->get('khipu_receiverid');
		}

                if (isset($this->request->post['khipu_secret'])) {
                        $data['khipu_secret'] = $this->request->post['khipu_secret'];
                } else {
                        $data['khipu_secret'] = $this->config->get('khipu_secret');
                }

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['khipu_geo_zone_id'])) {
			$data['khipu_geo_zone_id'] = $this->request->post['khipu_geo_zone_id'];
		} else {
			$data['khipu_geo_zone_id'] = $this->config->get('khipu_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['khipu_status'])) {
			$data['khipu_status'] = $this->request->post['khipu_status'];
		} else {
			$data['khipu_status'] = $this->config->get('khipu_status');
		}

		if (isset($this->request->post['khipu_sort_order'])) {
			$data['khipu_sort_order'] = $this->request->post['khipu_sort_order'];
		} else {
			$data['khipu_sort_order'] = $this->config->get('khipu_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('payment/khipu.tpl', $data));
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
