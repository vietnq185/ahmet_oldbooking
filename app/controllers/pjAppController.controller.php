<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();
	
	public $option_arr = array();
	
	public $defaultLocale = 'admin_locale_id';
	
	public $defaultForeignId = 1;
	
	public $defaultFields = 'front_fields';
	
	public $defaultFieldsIndex = 'fields_index';
	
	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}
		
		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}
		
		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);
	
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}
		
		return TRUE;
	}
	
	public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
	public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
    
    public function isInvoiceReady()
    {
        return $this->isAdmin();
    }
    
	public function isEditor()
	{
		return $this->getRoleId() == 2;
	}
    
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
		pjAppModel::factory()->prepare("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
    	
    	$baseDir = defined("PJ_INSTALL_PATH") ? PJ_INSTALL_PATH : null;
    	$dm = new pjDependencyManager($baseDir, PJ_THIRD_PARTY_PATH);		
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
		$this->appendJs('pjAdminCore.js');
		$this->appendCss('reset.css');
		 
		$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
		$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			$this->loadSetFields(true);
		}
    }
    
    public function getForeignId()
    {
    	return $this->defaultForeignId;
    }
    
    public static function setFields($locale)
    {
    	if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array('app/web/backup', 'app/web/upload', 'app/web/upload/fleets', 'app/web/upload/fleets/source', 'app/web/upload/fleets/thumb');
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}
	
	public function getTokens($option_arr, $booking_arr, $salt, $locale_id)
	{
        $name_titles = __('personal_titles', true, false);
        $payment_methods = __('payment_methods', true, false);

		$country = NULL;
		if (isset($booking_arr['c_country']) && !empty($booking_arr['c_country']))
		{
			$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
						->find($booking_arr['c_country'])->getData();
			if (!empty($country_arr))
			{
				$country = pjSanitize::clean($country_arr['country_title']);
			}
		}
		$price_first_transfer = (float)$booking_arr['price'];
        $return_arr = array();
        if(!empty($booking_arr['id']) && !empty($booking_arr['return_date']))
        {
            $return_arr = pjBookingModel::factory()
                ->select('id, price, c_address, t1.c_destination_address, c_departure_flight_time, c_flight_number, c_airline_company, c_notes, passengers')
                ->where('return_id', $booking_arr['id'])
                ->limit(1)
                ->findAll()
                ->getDataIndex(0);
        }
		$price_return_transfer = $return_arr ? (float)$return_arr['price'] : 0;
        $extra_arr = pjBookingExtraModel::factory()
            ->select('t1.quantity, t2.content as name, t3.content as info')
            ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
            ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$locale_id."'", 'left outer')
            ->where('t1.booking_id', $booking_arr['id'])
            ->findAll()
            ->getData();
        $extras = array();
        foreach($extra_arr as $extra)
        {
            $extras[] = "{$extra['quantity']} x {$extra['name']}" . (!empty($extra['info'])? " ({$extra['info']})": null);
        }
        $extras = implode(', ', $extras);

		$price_first_transfer = pjUtil::formatCurrencySign(number_format($price_first_transfer, 2), $option_arr['o_currency']);
		$price_return_transfer = pjUtil::formatCurrencySign(number_format($price_return_transfer, 2), $option_arr['o_currency']);
		$sub_total = pjUtil::formatCurrencySign(number_format($booking_arr['sub_total'], 2), $option_arr['o_currency']);
		$discount = pjUtil::formatCurrencySign(number_format($booking_arr['discount'], 2), $option_arr['o_currency']);
		$tax = pjUtil::formatCurrencySign(number_format($booking_arr['tax'], 2), $option_arr['o_currency']);
		$total = pjUtil::formatCurrencySign(number_format($booking_arr['total'], 2), $option_arr['o_currency']);
		$deposit = pjUtil::formatCurrencySign(number_format($booking_arr['deposit'], 2), $option_arr['o_currency']);
		$rest = pjUtil::formatCurrencySign(number_format($booking_arr['total'] - $booking_arr['deposit'], 2), $option_arr['o_currency']);

		$booking_date = $booking_time = NULL;
		if (isset($booking_arr['booking_date']) && !empty($booking_arr['booking_date']))
		{
			$tm = strtotime(@$booking_arr['booking_date']);
			$booking_date = date($option_arr['o_date_format'], $tm);
            $booking_time = date($option_arr['o_time_format'], $tm);
		}
		$return_date = $return_time = NULL;
		if (isset($booking_arr['return_date']) && !empty($booking_arr['return_date']))
		{
			$tm = strtotime(@$booking_arr['return_date']);
			$return_date = date($option_arr['o_date_format'], $tm);
            $return_time = date($option_arr['o_time_format'], $tm);
		}

        $duration = $distance = '';
        if(!empty($booking_arr['dropoff_id']))
        {
            $dropoff = pjDropoffModel::factory()->select('duration, distance')->find($booking_arr['dropoff_id'])->getData();
            if($dropoff)
            {
                $duration = $dropoff['duration'];
                $distance = $dropoff['distance'];
            }
        }
		
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionCancel&id='.@$booking_arr['id'].'&hash='.sha1(@$booking_arr['id'].@$booking_arr['created'].$salt);
		$paymentURL = @$booking_arr['o_site_url'].'?loadPayment=1&booking_uuid='.@$booking_arr['uuid'];

        $replace = array(
            '{Title}'       => isset($name_titles[$booking_arr['c_title']])? $name_titles[$booking_arr['c_title']]: '',
            '{FirstName}'   => pjSanitize::clean(@$booking_arr['c_fname']),
            '{LastName}'    => pjSanitize::clean(@$booking_arr['c_lname']),
            '{Email}'       => pjSanitize::clean(@$booking_arr['c_email']),
            '{Phone}'       => pjSanitize::clean(@$booking_arr['c_dialing_code'] . @$booking_arr['c_phone']),
            '{Country}'     => $country,

            '{UniqueID}'    => pjSanitize::clean(@$booking_arr['uuid']),
            '{Date}'        => $booking_date,
            '{Time}'        => $booking_time,
            '{From}'        => pjSanitize::clean(@$booking_arr['location']),
            '{To}'          => pjSanitize::clean(@$booking_arr['dropoff']),

            '{Passengers}'  => @$booking_arr['passengers'],
            '{Fleet}'       => pjSanitize::clean(@$booking_arr['fleet']),
            '{Duration}'    => $duration,
            '{Distance}'    => $distance,
            '{Hotel}'       => pjSanitize::clean(@$booking_arr['c_hotel']),
            '{Notes}'       => pjSanitize::clean(@$booking_arr['c_notes']),
            '{Extras}'      => ($extras),

            '{FlightNumber}'        => pjSanitize::clean(@$booking_arr['c_flight_number']),
            '{AirlineCompany}'      => pjSanitize::clean(@$booking_arr['c_airline_company']),
            '{DestinationAddress}'  => pjSanitize::clean(@$booking_arr['c_destination_address']),

            '{Address}'             => pjSanitize::clean(@$booking_arr['c_address']),
            '{FlightDepartureTime}' => @$booking_arr['c_departure_flight_time'],

            '{ReturnDate}'      => $return_date,
            '{ReturnTime}'      => $return_time,
            '{ReturnFrom}'      => pjSanitize::clean(@$booking_arr['dropoff']),
            '{ReturnTo}'        => pjSanitize::clean(@$booking_arr['location']),
            '{ReturnNotes}'     => pjSanitize::clean(@$return_arr['c_notes']),

            '{ReturnAddress}'               => pjSanitize::clean(@$return_arr['c_address']),
            '{ReturnFlightDepartureTime}'   => pjSanitize::clean(@$return_arr['c_departure_flight_time']),

            '{ReturnFlightNumber}'   => pjSanitize::clean(@$return_arr['c_flight_number']),
            '{ReturnAirlineCompany}' => pjSanitize::clean(@$return_arr['c_airline_company']),

            '{PaymentMethod}'   => isset($payment_methods[$booking_arr['payment_method']])? $payment_methods[$booking_arr['payment_method']]: '',
            '{PriceFirstTransfer}'        => $price_first_transfer,
	        '{PriceReturnTransfer}'        => $price_return_transfer,
	        '{SubTotal}'        => $sub_total,
            '{DiscountCode}'    => pjSanitize::clean(@$booking_arr['voucher_code']),
            '{Discount}'        => $discount,
            '{Tax}'             => $tax,
            '{Total}'           => $total,

            '{Deposit}' => $deposit,
            '{Rest}'    => $rest,
            '{CCOwner}' => pjSanitize::clean(@$booking_arr['cc_owner']),
            '{CCNum}'   => pjSanitize::clean(@$booking_arr['cc_num']),
            '{CCExp}'   => pjSanitize::clean(@$booking_arr['cc_exp']),
            '{CCSec}'   => pjSanitize::clean(@$booking_arr['cc_code']),

            '{CancelURL}' => $cancelURL,
        	'{DropoffAddress}' => @$booking_arr['c_destination_address'],
        	'{ReturnAddress}'             => pjSanitize::clean(@$return_arr['c_address']),
            '{ReturnDropoffAddress}' => @$return_arr['c_destination_address'],
        	'{PassengersReturn}'     => pjSanitize::clean(@$return_arr['passengers']),
            '{PaymentURL}' => $paymentURL
        );

		$search = array_keys($replace);

		return compact('search', 'replace');
	}
	
	public static function replaceTokens($booking_arr, $tokens, $message)
    {
        if(!empty($booking_arr) && !empty($tokens))
        {
            $is_airport = 0;
            if(!empty($booking_arr['location_id']))
            {
                $location = pjLocationModel::factory()->select('is_airport')->find($booking_arr['location_id'])->getData();
                $is_airport = $location['is_airport'];
            }
			$dropoff_arr = pjDropoffModel::factory()->find($booking_arr['dropoff_id'])->getData();
			if ($is_airport == 0 && $dropoff_arr && $dropoff_arr['is_airport'] == 0) {
				$message = str_replace(array('[FromLocationToLocation]', '[/FromLocationToLocation]'), array('', ''), $message);
				$message = pjUtil::removeTextBetweenToken('FromLocation', $message);
				$message = pjUtil::removeTextBetweenToken('FromAirport', $message);
			}
			else {
				$message = pjUtil::removeTextBetweenToken('FromLocationToLocation', $message);
				if ($is_airport) {
					$message = str_replace(array('[FromAirport]', '[/FromAirport]'), array('', ''), $message);
					$message = pjUtil::removeTextBetweenToken('FromLocation', $message);
				}
				else {
					$message = str_replace(array('[FromLocation]', '[/FromLocation]'), array('', ''), $message);
					$message = pjUtil::removeTextBetweenToken('FromAirport', $message);
				}
			}

			if (!empty($booking_arr['return_date'])) {
				$message = str_replace(array('[HasReturn]', '[/HasReturn]'), array('', ''), $message);
				if ($is_airport == 0 && $dropoff_arr && $dropoff_arr['is_airport'] == 0) {
					$message = str_replace(array('[ReturnToLocationToLocation]', '[/ReturnToLocationToLocation]'), array('', ''), $message);
					$message = pjUtil::removeTextBetweenToken('ReturnToLocation', $message);
					$message = pjUtil::removeTextBetweenToken('ReturnToAirport', $message);
				}
				else {
					$message = preg_replace('/\[ReturnToLocationToLocation\].*\[\/ReturnToLocationToLocation\]/s', '', $message);
					if ($is_airport) {
						$message = str_replace(array('[ReturnToAirport]', '[/ReturnToAirport]'), array('', ''), $message);
						$message = pjUtil::removeTextBetweenToken('ReturnToLocation', $message);
					}
					else {
						$message = str_replace(array('[ReturnToLocation]', '[/ReturnToLocation]'), array('', ''), $message);
						$message = pjUtil::removeTextBetweenToken('ReturnToAirport', $message);
					}
				}
			}
			else {
				$message = pjUtil::removeTextBetweenToken('HasReturn', $message);
				$message = pjUtil::removeTextBetweenToken('ReturnToAirport', $message);
				$message = pjUtil::removeTextBetweenToken('ReturnToLocation', $message);
				$message = pjUtil::removeTextBetweenToken('ReturnToLocationToLocation', $message);
			}

			if (floatval($booking_arr['deposit'])) {
				$message = str_replace(array('[HasDeposit]', '[/HasDeposit]'), array('', ''), $message);
			}
			else {
				$message = pjUtil::removeTextBetweenToken('HasDeposit', $message);
			}

			if (floatval($booking_arr['discount']) || !empty($booking_arr['voucher_code'])) {
				$message = str_replace(array('[HasDiscount]', '[/HasDiscount]'), array('', ''), $message);
			}
			else {
				$message = pjUtil::removeTextBetweenToken('HasDiscount', $message);
			}

            $message = str_replace($tokens['search'], $tokens['replace'], $message);
        }

        return $message;
    }
    
	public function getClientTokens($option_arr, $client, $salt, $locale_id)
	{
		$title = pjSanitize::clean($client['title']);
		$first_name = pjSanitize::clean($client['fname']);
		$last_name = pjSanitize::clean($client['lname']);
		$phone = pjSanitize::clean($client['phone']);
		$email = pjSanitize::clean($client['email']);
		$password = $client['password'];
	
		$search = array('{Title}', '{FirstName}', '{LastName}', '{Email}', '{Password}', '{Phone}');
		$replace = array($title, $first_name, $last_name, $email, $password, $phone);
	
		return compact('search', 'replace');
	}
	public function getAdminEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;	
	}
	
	public function getAdminPhone()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? (!empty($arr[0]['phone']) ? $arr[0]['phone'] : null) : null;	
	}
	
	public function pjActionAccountSend($option_arr, $client_id, $salt, $locale_id)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$Email->setContentType('text/html');
	
		$client = pjClientModel::factory()->find($client_id)->getData();
		$tokens = pjAppController::getClientTokens($option_arr, $client, PJ_SALT, $locale_id);
			
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		$locale_id = $this->getLocaleId();
	
		$admin_email = $this->getAdminEmail();
	
		if ($option_arr['o_email_client_account'] == 1)
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_client_account_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_client_account_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($client['email'])
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($lang_subject[0]['content'])
				->send(pjAppController::getEmailBody($message));
			}
		}
		if ($option_arr['o_admin_email_client_account'] == 1)
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_client_account_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_client_account_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($lang_subject[0]['content'])
				->send(pjAppController::getEmailBody($message));
			}
		}
	}
	public function pjActionForgotSend($option_arr, $client_id, $salt, $locale_id)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$Email->setContentType('text/html');
	
		$client = pjClientModel::factory()->find($client_id)->getData();
		$tokens = pjAppController::getClientTokens($option_arr, $client, PJ_SALT, $locale_id);
			
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		$locale_id = $this->getLocaleId();
	
		$admin_email = $this->getAdminEmail();
	
		$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_forgot_message')
			->limit(0, 1)
			->findAll()->getData();
		$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_forgot_subject')
			->limit(0, 1)
			->findAll()->getData();
	
		if (count($lang_message) === 1 && count($lang_subject) === 1)
		{
			$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);

			$Email
			->setTo($client['email'])
			->setFrom($admin_email, $option_arr['o_email_sender'])
			->setSubject($lang_subject[0]['content'])
			->send(pjAppController::getEmailBody($message));
		}
	}
	
	public function pjActionConfirmSend($option_arr, $booking_arr, $salt, $opt, $locale_id)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$Email->setContentType('text/html');
	
		$tokens = pjAppController::getTokens($option_arr, $booking_arr, PJ_SALT, $locale_id);
			
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		$admin_email = $this->getAdminEmail();
		$admin_phone = $this->getAdminPhone();
	
		if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_payment_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_payment_subject')
                ->limit(0, 1)
                ->findAll()->getData();

			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
				$message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
	
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
		if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_payment_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_payment_subject')
                ->limit(0, 1)
                ->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
		if(!empty($admin_phone) && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_sms_payment_message')
                ->limit(0, 1)
                ->findAll()->getData();
			if (count($lang_message) === 1)
			{
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);

				/*$params = array(
						'text' => $message,
						'type' => 'unicode',
						'key' => md5($option_arr['private_key'] . PJ_SALT)
				);
				$params['number'] = $admin_phone;
				$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));*/
                $this->messagebirdSendSMS(array($admin_phone), $message, $option_arr);
			}
		}

		if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_confirmation_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_confirmation_subject')
                ->limit(0, 1)
                ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
					
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
		if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_confirmation_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_confirmation_subject')
                ->limit(0, 1)
                ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
		if(!empty($admin_phone) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_sms_confirmation_message')
                ->limit(0, 1)
                ->findAll()->getData();
			if (count($lang_message) === 1)
			{
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);

				/*$params = array(
						'text' => $message,
						'type' => 'unicode',
						'key' => md5($option_arr['private_key'] . PJ_SALT)
				);
				$params['number'] = $admin_phone;
				$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));*/
                
                $this->messagebirdSendSMS(array($admin_phone), $message, $option_arr);
			}
		}
	
		if ($option_arr['o_email_arrival_confirmation'] == 1 && $opt == 'arrival')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_arrival_confirmation_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_arrival_confirmation_subject')
                ->limit(0, 1)
                ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
					
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
		if ($option_arr['o_admin_email_arrival_confirmation'] == 1 && $opt == 'arrival')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_arrival_confirmation_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_arrival_confirmation_subject')
                ->limit(0, 1)
                ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
		if(!empty($admin_phone) && $opt == 'arrival')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_sms_confirmation_message')
                ->limit(0, 1)
                ->findAll()->getData();
			if (count($lang_message) === 1)
			{
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);

				/*$params = array(
						'text' => $message,
						'type' => 'unicode',
						'key' => md5($option_arr['private_key'] . PJ_SALT)
				);
				$params['number'] = $admin_phone;
				$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));*/
                
                $this->messagebirdSendSMS(array($admin_phone), $message, $option_arr);
			}
		}
		
		if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_cancel_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_email_cancel_subject')
                ->limit(0, 1)
                ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
	
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
		if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_cancel_message')
                ->limit(0, 1)
                ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
                ->where('t1.model','pjOption')
                ->where('t1.locale', $locale_id)
                ->where('t1.field', 'o_admin_email_cancel_subject')
                ->limit(0, 1)
                ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
			    $subject = pjAppController::replaceTokens($booking_arr, $tokens, $lang_subject[0]['content']);
                $message = pjAppController::replaceTokens($booking_arr, $tokens, $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email, $option_arr['o_email_sender'])
				->setSubject($subject)
				->send(pjAppController::getEmailBody($message));
			}
		}
	}

    public static function getDiscount($subtotal, $voucher_code, $currency)
    {
        if (!isset($voucher_code) || empty($voucher_code))
        {
            // Missing params
            return array('status' => 'ERR', 'code' => 100, 'text' => 'Voucher code couldn\'t be empty.');
        }
        $arr = pjVoucherModel::factory()
            ->where('t1.code', $voucher_code)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (empty($arr))
        {
            // Not found
            return array('status' => 'ERR', 'code' => 101, 'text' => 'Voucher not found.');
        }

        $now = time();
        $dateTs = strtotime('00:00');
        $dateYmd = date('Y-m-d');

        $valid = false;
        switch ($arr['valid'])
        {
            case 'fixed':
                $time_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
                $time_to = strtotime($arr['date_to'] . " " . $arr['time_to']);
                if ($time_from <= $now && $time_to >= $now)
                {
                    // Valid
                    $valid = true;
                }
                break;
            case 'period':
                $d_from = strtotime($arr['date_from']);
                $d_to = strtotime($arr['date_to']);
                $t_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
                $t_to = strtotime($arr['date_to'] . " " . $arr['time_to']);
                if ($d_from <= $dateTs && $d_to >= $dateTs && $t_from <= $now && $t_to >= $now)
                {
                    // Valid
                    $valid = true;
                }
                break;
            case 'recurring':
                $t_from = strtotime($dateYmd . " " . $arr['time_from']);
                $t_to = strtotime($dateYmd . " " . $arr['time_to']);
                if ($arr['every'] == strtolower(date("l", $now)) && $t_from <= $now && $t_to >= $now)
                {
                    // Valid
                    $valid = true;
                }
                break;
        }

        if (!$valid)
        {
            // Out of date
            return array('status' => 'ERR', 'code' => 102, 'text' => 'Voucher code is out of date.');
        }

        // Valid
        $discount = $discount_print = 0;
        switch ($arr['type'])
        {
            case 'percent':
                $discount = ($subtotal * $arr['discount']) / 100;
                $discount_print = number_format($discount, 2, ',', ' ') . ' ' . $currency . ' (' . $arr['discount'] . '%)';
                break;
            case 'amount':
                $discount = $arr['discount'];
                $discount_print = number_format($discount, 2, ',', ' ') . ' ' . $currency;
                break;
        }

        return array(
            'status' => 'OK',
            'code' => 200,
            'text' => 'Voucher code has been applied.',
            'voucher_code' => $arr['code'],
            'discount_print' => $discount_print,
            'discount' => $discount,
        );
    }

    public static function getEmailBody($message) // TODO: Delete, if will not be used.
    {
        return $message;

        $body = pjUtil::fileGetContents(PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionEmailTemplate');
        return str_replace('{EMAIL_BODY}', $message, $body);
    }
    
    public function messagebirdSendSMS($recipients, $body, $option_arr) {
    	require_once(PJ_COMPONENTS_PATH. '/messagebird/autoload.php');
    	if (!$recipients) {
    	    $recipients = array();
    	}
    	$MessageBird = new \MessageBird\Client($option_arr['o_message_bird_access_key']);
		$Message             = new \MessageBird\Objects\Message();
		$Message->originator = $option_arr['o_message_bird_originator'];
		$Message->recipients = $recipients;
		$Message->body       = $body;
		$Message->datacoding = 'unicode';
		
		try {
		   	$MessageResult = $MessageBird->messages->create($Message);
		   	$pjSmsModel = pjSmsModel::factory();
		   	foreach ($recipients as $number) {
		   		$data = array();
		   		$data['number'] = $number;
		   		$data['text'] = $body;
		   		$data['status'] = 'sent';
		   		$pjSmsModel->reset()->setAttributes($data)->insert();
		   	}
		   	return true;		
		} catch (\MessageBird\Exceptions\AuthenticateException $e) {
		    // That means that your accessKey is unknown
		    $this->log('wrong login');	
		    return false;	
		} catch (\MessageBird\Exceptions\BalanceException $e) {
		    // That means that you are out of credits, so do something about it.
		    $this->log('no balance');	
		    return false;	
		} catch (\Exception $e) {
			$this->log($e->getMessage());
			return false;
		}
    }
    
    public function getFleetDiscount($date, $fleet_id, $price_level=1) {
		$fleet_discount_arr = pjFleetDiscountModel::factory()
			->reset()
			->select('
				t1.*,
				IF (t2.id IS NOT NULL, t2.type, t1.type) as type,
				IF (t2.id IS NOT NULL, t2.discount, t1.discount) as discount,
				IF (t2.id IS NOT NULL, t2.is_subtract, t1.is_subtract) as is_subtract
			')
			->join('pjFleetDiscountPeriod', 't2.fleet_discount_id = t1.id', 'left')
			->where('t1.fleet_id', $fleet_id)
			->where('t1.day', strtolower(date('l', strtotime($date))))
			->where('t1.price_level', $price_level)
			->where('((t1.valid="period" AND "' . $date . '" BETWEEN t2.date_from AND t2.date_to) OR t1.valid="always")')
			->having('discount > 0')
			->findAll()
			->getDataIndex(0);

		return $fleet_discount_arr;
    }
    
    public function getLocationByIp($ip)
    {
        $ch = curl_init('http://ipwhois.app/json/' . $ip);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        // Decode JSON response
        $ipWhoIsResponse = json_decode($json, true);
        // Country code output, field "country_code"
        return $ipWhoIsResponse;
    }
    
    static public function createRandomBookingId() {
        mt_srand();
        $uuid = date('y',time()).mt_rand(10000000, 99999999);
        $cnt = pjBookingModel::factory()->reset()->where('t1.uuid', $uuid)->findCount()->getData();
        if ((int)$cnt > 0)
        {
            $this->createRandomBookingId();
        } else {
            return $uuid;
        }
    }
    
    protected function pjActionGenerateInvoice($order_id)
    {
        if (!isset($order_id) || (int) $order_id <= 0)
        {
            return array('status' => 'ERR', 'code' => 400, 'text' => 'ID is not set ot invalid.');
        }
        $arr = pjBookingModel::factory()->find($order_id)->getData();
        if (empty($arr))
        {
            return array('status' => 'ERR', 'code' => 404, 'text' => 'Order not found.');
        }
        
        $items = array();
        
        $items[] = array(
            'name' => 'Booking payment',
            'description' => "",
            'qty' => 1,
            'unit_price' => $arr['total'],
            'amount' => $arr['total']
        );
        
        $map = array(
            'confirmed' => 'paid',
            'cancelled' => 'cancelled',
            'in_progress' => 'not_paid',
            'passed_on' => 'not_paid',
            'pending' => 'not_paid'
        );
        if ($arr['status'] == 'confirmed') {
            $paid_deposit = (float)$arr['deposit'];
            $amount_due = (float)$arr['total'] - $paid_deposit;
        } else {
            $paid_deposit = 0;
            $amount_due = (float)$arr['total'];
        }
        $response = $this->requestAction(
            array(
                'controller' => 'pjInvoice',
                'action' => 'pjActionCreate',
                'params' => array(
                    'key' => md5($this->option_arr['private_key'] . PJ_SALT),
                    'uuid' => pjUtil::uuid(),
                    'order_id' => $arr['uuid'],
                    'foreign_id' => $this->getForeignId(),
                    'issue_date' => ':CURDATE()',
                    'due_date' => ':CURDATE()',
                    'created' => ':NOW()',
                    //'modified' => ':NULL',
                    'status' => @$map[$arr['status']],
                    'subtotal' => $arr['sub_total'],
                    'discount' => $arr['discount'],
                    'tax' => $arr['tax'],
                    'shipping' => $arr['credit_card_fee'],
                    'total' => $arr['total'],
                    'paid_deposit' => $paid_deposit,
                    'amount_due' => $amount_due,
                    'payment_method' => $arr['payment_method'],
                    'currency' => $this->option_arr['o_currency'],
                    'notes' => $arr['c_notes'],
                    'b_billing_address' => $arr['c_address'],
                    'b_name' => $arr['c_fname'].' '.$arr['c_lname'],
                    'b_address' => $arr['c_address'],
                    'b_street_address' => '',
                    'b_city' => $arr['c_city'],
                    'b_state' => $arr['c_state'],
                    'b_zip' => $arr['c_zip'],
                    'b_country' => $arr['c_country'],
                    'b_phone' => $arr['c_phone'],
                    'b_email' => $arr['c_email'],
                    'b_url' => '',
                    's_shipping_address' => $arr['c_destination_address'],
                    's_name' => $arr['c_fname'].' '.$arr['c_lname'],
                    's_address' => $arr['c_destination_address'],
                    's_street_address' => '',
                    's_city' => $arr['c_city'],
                    's_state' => $arr['c_state'],
                    's_zip' => $arr['c_zip'],
                    's_country' => $arr['c_country'],
                    's_phone' => $arr['c_phone'],
                    's_email' => $arr['c_email'],
                    's_url' => '',
                    'items' => $items
                )
            ),
            array('return')
            );
        return $response;
    }
}
?>