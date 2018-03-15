<?php

class CloudPaymentsAPI {

	protected $curl = null;
	
	/**
	 * Проверяем айпи адреса с которых пришли запросы
	 */
	private function CheckAllowedIps()
	{
		return true;
		// убрали проверку по айпи
		if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '130.193.70.192', '185.98.85.109'])) throw new Exception('CloudPayments: Hacking atempt!');
	}
	
	/**
	 * Проверяем коректность запроса
	 */
	private function CheckHMAC($sSercet)
	{
		if (!$sSercet) throw new Exception('CloudPayments: Sercet key is not defined');
		$sPostData    = file_get_contents('php://input');
		$sCheckSign   = base64_encode(hash_hmac('SHA256', $sPostData, $sSercet, true));
		$sRequestSign = isset($_SERVER['HTTP_CONTENT_HMAC']) ? $_SERVER['HTTP_CONTENT_HMAC'] : '';
		if ($sCheckSign !== $sRequestSign) {
			throw new Exception('CloudPayments: Hacking atempt!');
		};
		return true;
	}
	
	/**
	 * Проверяем сумму заказа перед совершением платежа
	 *
	 * @since version
	 */
	public function Check($fAmount)
	{
		$this->CheckAllowedIps();
		$this->CheckHMAC(variable_get('cloudpayments_api_password'));
		if ((float)$fAmount != (float)$_POST['Amount']) exit('{"code":11}'); // Неверная сумма
		exit('{"code":0}');
	}
	
	/**
	 * SMS: Меняем статус заказа на оплачено
	 * DMS: Меняем статус заказа на авторизовано
	 */
	public function Pay()
	{
		$this->CheckAllowedIps();
		$this->CheckHMAC(variable_get('cloudpayments_api_password'));
		// добавляем транзакцию в базу
		$aField = [];
		$oOrder = commerce_order_load($_POST['InvoiceId']);
		$wrapper = entity_metadata_wrapper('commerce_order', $oOrder);
		$currency_code = $wrapper->commerce_order_total->currency_code->value();
		$amount_not_formated = $wrapper->commerce_order_total->amount->value();
		$amount = commerce_currency_amount_to_decimal($amount_not_formated, $currency_code);
		$sScheme = variable_get('cloudpayments_scheme');
		$aField['user_id'] = $oOrder->uid;
		$aField['mail'] = $oOrder->mail;
		$aField['order_id'] = $oOrder->order_id;
		$aField['order_id'] = $oOrder->order_id;
		$aField['amount'] = $amount;
		$aField['created'] = time();
		$aField['status'] =  $sScheme == 'sms' ? variable_get('cloudpayments_status_success') : variable_get('cloudpayments_status_cp_authorized');
		$aField['data'] = serialize($_POST);
		$aField['transaction_id'] = $_POST['TransactionId'];
		db_insert('cloudpayments_transaction')->fields($aField)->execute();
		// обновляем статус заказа
		$oOrder->status = $aField['status'];
		commerce_order_save($oOrder);
		exit('{"code":0}');
	}
	
	/**
	 * Меняем статус заказа на оплачено при DMS
	 */
	public function Confirm()
	{
		$this->CheckAllowedIps();
		$this->CheckHMAC(variable_get('cloudpayments_api_password'));
		$sScheme = variable_get('cloudpayments_scheme');
		$oOrder = commerce_order_load($_POST['InvoiceId']);
		if($sScheme == 'sms') {
			throw new Exception('СloudPayments: This method used only for DMS scheme');
		} else if($sScheme == 'dms') {
			// обновляем статус заказа
			$oOrder->status = variable_get('cloudpayments_status_cp_confirmed');
			commerce_order_save($oOrder);
		} else {
			throw new Exception('CloudPayments: Undefined scheme of payments!');
		}
		exit('{"code":0}');
	}
	
	/**
	 * Меняем статус заказа на возврат
	 */
	public function Refund()
	{
		$this->CheckAllowedIps();
		$this->CheckHMAC(variable_get('cloudpayments_api_password'));
		$oOrder = commerce_order_load($_POST['InvoiceId']);
		$oOrder->status = variable_get('cloudpayments_status_cp_refund');
		commerce_order_save($oOrder);
		exit('{"code":0}');
	}
	
	/**
	 * Метод для отправки запросов системе
	 * @param string $location
	 * @param array  $request
	 * @return bool|array
	 */
	public function MakeRequest($location, $request = array()) {
		if (!$this->curl) {
			$auth       = variable_get('cloudpayments_public_id') . ':' . variable_get('cloudpayments_api_password');
			$this->curl = curl_init();
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($this->curl, CURLOPT_USERPWD, $auth);
		}
		
		curl_setopt($this->curl, CURLOPT_URL, 'https://api.cloudpayments.ru/' . $location);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
			"content-type: application/json"
		));
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($request));
		
		$response = curl_exec($this->curl);
		if ($response === false || curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 200) {
//			vmDebug('CloudPayments Failed API request' .
//				' Location: ' . $location .
//				' Request: ' . print_r($request, true) .
//				' HTTP Code: ' . curl_getinfo($this->curl, CURLINFO_HTTP_CODE) .
//				' Error: ' . curl_error($this->curl)
//			);
			
			return false;
		}
		$response = json_decode($response, true);
		if (!isset($response['Success']) || !$response['Success']) {
			drupal_set_message('CloudPayments error: '.$response['Message'], 'error');
//			vmDebug('CloudPayments Failed API request' .
//				' Location: ' . $location .
//				' Request: ' . print_r($request, true) .
//				' HTTP Code: ' . curl_getinfo($this->curl, CURLINFO_HTTP_CODE) .
//				' Error: ' . curl_error($this->curl)
//			);
			
			return false;
		}
		
		return $response;
	}
}