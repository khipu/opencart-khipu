<?php 
class ModelExtensionPaymentKhipu extends Model {
  	public function getMethod($address, $total) {

		$this->load->language('extension/payment/khipu');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_khipu_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if (!$this->config->get('payment_khipu_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	

		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'khipu',
        		'title'      => '<img src="https://bi.khipu.com/150x50/capsule/khipu/transparent/'.$this->config->get('payment_khipu_receiverid').'"> ' . $this->language->get('text_title'),
                'terms'      => '',
				'sort_order' => $this->config->get('payment_khipu_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>
