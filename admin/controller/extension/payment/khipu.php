<?php
class ControllerExtensionPaymentKhipu extends Controller {
	private $error = array();

	public function index() {
        $this->load->language('extension/payment/khipu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('payment_khipu', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
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


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/khipu', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/khipu', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['khipu_receiverid'])) {
			$data['payment_khipu_receiverid'] = $this->request->post['payment_khipu_receiverid'];
		} else {
			$data['payment_khipu_receiverid'] = $this->config->get('payment_khipu_receiverid');
		}

        if (isset($this->request->post['payment_khipu_secret'])) {
                $data['payment_khipu_secret'] = $this->request->post['payment_khipu_secret'];
        } else {
                $data['payment_khipu_secret'] = $this->config->get('payment_khipu_secret');
        }

        if (isset($this->request->post['payment_khipu_completed_status_id'])) {
            $data['payment_khipu_completed_status_id'] = $this->request->post['payment_khipu_completed_status_id'];
        } else {
            $data['payment_khipu_completed_status_id'] = $this->config->get('payment_khipu_completed_status_id');
        }

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_khipu_geo_zone_id'])) {
			$data['payment_khipu_geo_zone_id'] = $this->request->post['payment_khipu_geo_zone_id'];
		} else {
			$data['payment_khipu_geo_zone_id'] = $this->config->get('payment_khipu_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_khipu_status'])) {
            $data['payment_khipu_status'] = $this->request->post['payment_khipu_status'];
        } else {
            $data['payment_khipu_status'] = $this->config->get('payment_khipu_status');
        }
		
		if (isset($this->request->post['payment_khipu_sort_order'])) {
			$data['payment_khipu_sort_order'] = $this->request->post['payment_khipu_sort_order'];
		} else {
			$data['payment_khipu_sort_order'] = $this->config->get('payment_khipu_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');



		$this->response->setOutput($this->load->view('extension/payment/khipu', $data));
	}

	private function validate() {

		if (!$this->user->hasPermission('modify', 'extension/payment/khipu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_khipu_receiverid']) {
			$this->error['receiverid'] = $this->language->get('error_receiverid');
		}

        if (!$this->request->post['payment_khipu_secret']) {
                $this->error['secret'] = $this->language->get('error_secret');
        }

        return !$this->error;
	}
}
?>
