<?php

/**
 * Payment method callback: settings form.
 */
function cloudpayments_admin_settings() {
	$form = array();
	
	$aStatus = commerce_order_status_options_list();
	$form['cloudpayments_public_id'] = array(
		'#type' => 'textfield',
		'#title' => 'Public ID',
		'#default_value' => variable_get('cloudpayments_public_id',''),
	);

	$form['cloudpayments_api_password'] = array(
		'#type' => 'textfield',
		'#title' => 'Password for API',
		'#default_value' => variable_get('cloudpayments_api_password',''),
	);

	$form['cloudpayments_scheme'] = array(
		'#type' => 'select',
		'#title' => t('Payment scheme'),
		'#default_value' => variable_get('cloudpayments_scheme','sms'),
		'#options' => array(
			'sms' => 'SMS',
			'dms' => 'DMS',
		),
	);

	$form['cloudpayments_send_check'] = array(
		'#type' => 'select',
		'#title' => t('Send receipt'),
		'#default_value' => variable_get('cloudpayments_send_check','yes'),
		'#options' => array(
			'yes' => 'Да',
			'no' => 'Нет',
		),
	);

	$form['cloudpayments_localization'] = array(
		'#type' => 'select',
		'#title' => t('Widget localization'),
		'#default_value' => variable_get('cloudpayments_localization','ru-RU'),
		'#options' => array(
			"ru-RU"=> 'Русский',
			"en-US"=>'Английский',
			"lv"=>'Латышский',
			"az"=>'Азербайджанский',
			"kk-KZ"=>'Казахский',
			"uk"=>'Украинский',
			"pl"=>'Польский',
			"pt"=>'Португальский',
		),
	);

	$form['cloudpayments_tax_system'] = array(
		'#type' => 'select',
		'#title' => t('Taxation system'),
		'#default_value' => variable_get('cloudpayments_tax_system',0),
		'#options' => array(
			'0' => t('0 — General tax system'),
			'1' => t('1 — Simplified taxation system'),
			'2' => t('2 — Simplified taxation system'),
			'3' => t('3 — Single tax on imputed earnings'),
			'4' => t('4 — Single agricultural tax'),
			'5' => t('5 — Patent taxation system')
		)
	);

	$form['cloudpayments_tax_product'] = array(
		'#type' => 'select',
		'#title' => t('Product tax'),
		'#default_value' => variable_get('cloudpayments_tax_product',0),
		'#options' => array(
			'-1' => 'VAT Free',
			'0' => 'VAT 0%',
			'10' => 'VAT 10%',
			'18' => 'VAT 18%',
			'110' => 'VAT 10/110',
			'118' => 'VAT 18/118',
			'-2' => t('Take from product')
		)
	);


	$form['cloudpayments_tax_shipping'] = array(
		'#type' => 'select',
		'#title' => t('Shipping tax'),
		'#default_value' => variable_get('cloudpayments_tax_shipping',0),
		'#options' => array(
			'-1' => 'VAT Free',
			'0' => 'VAT 0%',
			'10' => 'VAT 10%',
			'18' => 'VAT 18%',
			'110' => 'VAT 10/110',
			'118' => 'VAT 18/118',
			'-2' => t('Take from product')
		)
	);
	
	$form['cloudpayments_status_success'] = array(
		'#type' => 'select',
		'#title' => t('Order payed successfully'),
		'#default_value' => variable_get('cloudpayments_status_success',''),
		'#options' => $aStatus,
	);
	
	$form['cloudpayments_status_pending'] = array(
		'#type' => 'select',
		'#title' => t('The status of the new (unpaid) order'),
		'#default_value' => variable_get('cloudpayments_status_pending',''),
		'#options' => $aStatus,
	);

	$form['cloudpayments_status_cp_authorized'] = array(
		'#type' => 'select',
		'#title' => t('Authorized payment status (DMS)'),
		'#default_value' => variable_get('cloudpayments_status_cp_authorized',''),
		'#options' => $aStatus,
	);

	$form['cloudpayments_status_cp_confirmed'] = array(
		'#type' => 'select',
		'#title' => t('Confirmed payment status (DMS)'),
		'#default_value' => variable_get('cloudpayments_status_cp_confirmed',''),
		'#options' => $aStatus,
	);

	$form['cloudpayments_status_canceled'] = array(
		'#type' => 'select',
		'#title' => t('Order cancelled (DMS)'),
		'#default_value' => variable_get('cloudpayments_status_canceled',''),
		'#options' => $aStatus,
	);
	
	$form['cloudpayments_status_refund'] = array(
		'#type' => 'select',
		'#title' => t('Refund order'),
		'#default_value' => variable_get('cloudpayments_status_refund',''),
		'#options' => $aStatus,
	);
	
	$form_state = array();
	$fields = array();
	$profile = commerce_customer_profile_new('billing');
	field_attach_form('commerce_customer_profile', $profile, $fields, $form_state);
	$aField = ['' => '---'];
	foreach($form_state['field'] as $sKey => $mValue) {
		$aField[$sKey] = $sKey;
	}
	drupal_get_messages('error'); // получим ошибки, чтобы их очистить и не выводить пользователю.

	$form['cloudpayments_field_phone'] = array(
		'#type' => 'select',
		'#title' => t('Field with phone'),
		'#default_value' => variable_get('cloudpayments_field_phone',''),
		'#options' => $aField,
	);
	
	return system_settings_form($form);
}

function cloudpayments_admin_settings_validate($form, &$form_state) {

}