<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminInquiryGenerator extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
		    if (isset($_POST['send_inquiry']))
		    {
		        $pjEmail = new pjEmail();
		        if ($this->option_arr['o_send_email'] == 'smtp')
		        {
		            $pjEmail
		            ->setTransport('smtp')
		            ->setSmtpHost($this->option_arr['o_smtp_host'])
		            ->setSmtpPort($this->option_arr['o_smtp_port'])
		            ->setSmtpUser($this->option_arr['o_smtp_user'])
		            ->setSmtpPass($this->option_arr['o_smtp_pass'])
		            ;
		        }
		        $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
		        $pjEmail->setContentType('text/html');
		        $pjEmail
		        ->setTo($_POST['to'])
		        ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
		        ->setSubject($_POST['i18n'][$locale_id]['subject']);
		        $body = pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message']);
		        if ($pjEmail->send($body))
		        {
		            $err = 'AB19';
		        } else {
		            $err = 'AB18';
		        }
		        pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminInquiryGenerator&action=pjActionIndex&err=$err");
		    } else {
			    $this->set('inquiry_template_arr', pjEmailThemeModel::factory()
			        ->join('pjMultiLang', "t2.model='pjEmailTheme' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			        ->select("t1.*, t2.content as name")
			        ->where('t1.status', 'T')
			        ->where('t1.type', 'inquiry')
			        ->orderBy("name ASC")
			        ->findAll()->getData());
			    
			    $pickup_arr = pjLocationModel::factory()
			    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			    ->select("t1.*, t2.content as pickup_location")
			    ->where('t1.status', 'T')
			    ->orderBy("is_airport DESC, pickup_location ASC")
			    ->findAll()->getDataPair('id');
			    $this->set('pickup_arr', $pickup_arr);
			    
			    $this->set('fleet_arr', pjFleetModel::factory()
			        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			        ->select("t1.*, t2.content as fleet")
			        ->where('t1.status', 'T')
			        ->orderBy("fleet ASC")
			        ->findAll()->getData());
			    
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('pjAdminInquiryGenerator.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetDropoff()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        $location_id = $_GET['location_id'];
	        
	        $dropoff_arr = pjDropoffModel::factory()
	        ->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	        ->select("t1.*, t2.content as location")
	        ->where('t1.location_id', $location_id)
	        ->orderBy("location ASC")
	        ->findAll()->getData();
	        $this->set('dropoff_arr', $dropoff_arr);
	    }
	}
	
	public function pjActionCalPrice()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        if(count(explode(" ", $_POST['booking_date'])) == 3)
	        {
	            list($date, $time, $period) = explode(" ", $_POST['booking_date']);
	        }else{
	            list($date, $time) = explode(" ", $_POST['booking_date']);
	        }
	        $date = pjUtil::formatDate(@$date, $this->option_arr['o_date_format']);
	        $dayIndex = $date? date('N', strtotime($date)): null;
	        
	        $drop_arr = pjDropoffModel::factory()->find($_POST['dropoff_id'])->getData();
	        $price_level = $drop_arr ? $drop_arr['price_level'] : 1;
	        $fleet = pjFleetModel::factory()->find($_POST['fleet_id'])->getData();
	        
	        $price_arr = pjPriceModel::factory()
	        ->select("t1.price_{$dayIndex}, t2.return_discount_{$dayIndex}, t2.return_discount_{$dayIndex}_2")
	        ->join('pjFleet', 't2.id = t1.fleet_id')
	        ->where('t1.dropoff_id', $_POST['dropoff_id'])
	        ->where('t1.fleet_id', $_POST['fleet_id'])
	        ->findAll()
	        ->getDataIndex(0);
	        
	        $one_way_price = $price_arr["price_{$dayIndex}"];
	        $fleet_discount_arr = $this->getFleetDiscount($date, $_POST['fleet_id'], $price_level);
	        if ($fleet_discount_arr) {
	            if ($fleet_discount_arr['is_subtract'] == 'T') {
	                if ($fleet_discount_arr['type'] == 'amount') {
	                    $one_way_price = $one_way_price - $fleet_discount_arr['discount'];
	                } else {
	                    $one_way_price = $one_way_price - (($one_way_price * $fleet_discount_arr['discount']) / 100);
	                }
	            } else {
	                if ($fleet_discount_arr['type'] == 'amount') {
	                    $one_way_price = $one_way_price + $fleet_discount_arr['discount'];
	                } else {
	                    $one_way_price = $one_way_price + (($one_way_price * $fleet_discount_arr['discount']) / 100);
	                }
	            }
	            if ($one_way_price < 0) {
	                $one_way_price = 0;
	            }
	        }
	        $return_price = $one_way_price;
	        
	        $return_discount = $price_level == 1 ? $price_arr["return_discount_{$dayIndex}"] : $price_arr["return_discount_{$dayIndex}_2"];
	        $result = pjUtil::calPrice($one_way_price, $return_price, isset($_POST['has_return']) && !empty($_POST['return_date']) ? true : false, $return_discount, $this->option_arr, '', '');
	        $total = $result['total'];
	        $result['price'] = 0;
	        $result['price_first_transfer'] = 0;
	        $result['price_return_transfer'] = 0;
	        if (isset($_POST['has_return']) && !empty($_POST['return_date'])) {
	            $result['price_first_transfer'] = $total/2;
	            $result['price_return_transfer'] = $total/2;
	        } else {
	            $result['price'] = $total;
	        }
	        pjAppController::jsonResponse($result);
	        exit;
	    }
	}
	
	public function pjActionGenerateInquiry() {
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
    	    $i18n = pjMultiLangModel::factory()->getMultiLang((int)$_POST['inquiry_template'], 'pjEmailTheme');
    	    $locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
    	    ->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
    	    ->where('t2.file IS NOT NULL')
    	    ->orderBy('t1.sort ASC')->findAll()->getData();
    	    
    	    $fleet_i18n = pjMultiLangModel::factory()->getMultiLang($_POST['fleet_id'], 'pjFleet');
    	    $pickup_i18n = pjMultiLangModel::factory()->getMultiLang($_POST['location_id'], 'pjLocation');
    	    $dropoff_i18n = pjMultiLangModel::factory()->getMultiLang($_POST['dropoff_id'], 'pjDropoff');
    	    
    	    $lp_arr = $i18n_arr = array();
    	    foreach ($locale_arr as $item)
    	    {
    	        $lp_arr[$item['id']."_"] = $item['file'];
    	        $_POST['fleet'] = $fleet_i18n[$item['id']]['fleet'];
    	        $_POST['location'] = $pickup_i18n[$item['id']]['pickup_location'];
    	        $_POST['dropoff'] = $dropoff_i18n[$item['id']]['location'];
    	        
    	        $lang_subject = pjAppController::replaceTokens($_POST, pjAppController::getInquiryTokens($this->option_arr, $_POST, $item['id']), $i18n[$item['id']]['subject']);
    	        $i18n_arr[$item['id']]['subject'] = $lang_subject;
    	        
    	        $lang_message = pjAppController::replaceTokens($_POST, pjAppController::getInquiryTokens($this->option_arr, $_POST, $item['id']), $i18n[$item['id']]['body']);
    	        $i18n_arr[$item['id']]['message'] = $lang_message;
    	    }
    	    $this->set('i18n_arr', $i18n_arr);
    	    $this->set('lp_arr', $locale_arr);
	    }
	}
}
?>