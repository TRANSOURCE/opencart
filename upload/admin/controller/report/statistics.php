<?php
namespace Opencart\Admin\Controller\Report;
class Statistics extends \Opencart\System\Engine\Controller {
	private $error = [];
	
	public function index() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('report/statistics');

		$this->getList();	
	}
	
	public function ordersale() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('report/statistics');		

		if ($this->validate()) {
			$this->load->model('sale/order');
			
			$this->model_report_statistics->editValue('order_sale', $this->model_sale_order->getTotalSales(['filter_order_status' => implode(',', array_merge($this->config->get('config_complete_status'), $this->config->get('config_processing_status')))]));
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']));
		}
		
		$this->getList();	
	}
		
	public function orderprocessing() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('report/statistics');		

		if ($this->validate()) {
			$this->load->model('sale/order');
			
			$this->model_report_statistics->editValue('order_processing', $this->model_sale_order->getTotalOrders(['filter_order_status' => implode(',', $this->config->get('config_processing_status'))]));
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']));
		}
		
		$this->getList();	
	}
	
	public function ordercomplete() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));		
		
		$this->load->model('report/statistics');
		
		if ($this->validate()) {
			$this->load->model('sale/order');
			
			$this->model_report_statistics->editValue('order_complete', $this->model_sale_order->getTotalOrders(['filter_order_status' => implode(',', $this->config->get('config_complete_status'))]));
		
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']));
		}		
		
		$this->getList();	
	}
	
	public function orderother() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));	
		
		$this->load->model('report/statistics');
		
		if ($this->validate()) {
			$this->load->model('localisation/order_status');
				
			$order_status_data = [];
	
			$results = $this->model_localisation_order_status->getOrderStatuses();
	
			foreach ($results as $result) {
				if (!in_array($result['order_status_id'], array_merge($this->config->get('config_complete_status'), $this->config->get('config_processing_status')))) {
					$order_status_data[] = $result['order_status_id'];
				}
			}		
			
			$this->load->model('sale/order');
			
			$this->model_report_statistics->editValue('order_other', $this->model_sale_order->getTotalOrders(['filter_order_status' => implode(',', $order_status_data)]));
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']));
		}
		
		$this->getList();	
	}

	public function returns() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));	
				
		$this->load->model('report/statistics');
		
		if ($this->validate()) {
			$this->load->model('sale/returns');
			
			$this->model_report_statistics->editValue('return', $this->model_sale_returns->getTotalReturns(['filter_return_status_id' => $this->config->get('config_return_status_id')]));
		
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']));
		}
		
		$this->getList();	
	}

	public function product() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));	
				
		$this->load->model('report/statistics');
		
		if ($this->validate()) {		
			$this->load->model('catalog/product');
			
			$this->model_report_statistics->editValue('product', $this->model_catalog_product->getTotalProducts(['filter_quantity' => 0]));

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']));
		}
		
		$this->getList();
	}	
	
	public function review() {
		$this->load->language('report/statistics');

		$this->document->setTitle($this->language->get('heading_title'));	
				
		$this->load->model('report/statistics');	
		
		if ($this->validate()) {	
			$this->load->model('catalog/review');
				
			$this->model_report_statistics->editValue('review', $this->model_catalog_review->getTotalReviewsAwaitingApproval());
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token']));
		}

		$this->getList();
	}
	
	public function getList() {
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token'])
		];

		$data['statistics'] = [];
		
		$this->load->model('report/statistics');
		
		$results = $this->model_report_statistics->getStatistics();
		
		foreach ($results as $result) {
			$data['statistics'][] = [
				'name'  => $this->language->get('text_' . $result['code']),
				'value' => $result['value'],
				'href'  => $this->url->link('report/statistics|' . str_replace('_', '', $result['code']), 'user_token=' . $this->session->data['user_token'])
			];
		}

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
							
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/statistics', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'report/statistics')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}	
}
