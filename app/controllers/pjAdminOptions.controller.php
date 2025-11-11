<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
			
			$this->set('arr', $arr);

			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();

			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionBooking()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionBookingForm()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionArrivalNotice()
	{
		$this->checkLogin();

		if ($this->isAdmin()) {
			if (!empty($_POST['update'])) {
			    pjArrivalNoticeModel::factory()->reset()->eraseAll();

				if (!empty($_POST['certain_dates'])) {
					foreach ($_POST['certain_dates'] as $dates) {
					    pjArrivalNoticeModel::factory()
							->reset()
							->setAttributes(array(
								'date_from' => pjUtil::formatDate($dates['date_from'], $this->option_arr['o_date_format']),
								'date_to' => pjUtil::formatDate($dates['date_to'], $this->option_arr['o_date_format'])
							))
							->insert();
					}
				}

				pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjAdminOptions&action=pjActionArrivalNotice&err=AOAN01');
			}

			$certainDates = pjArrivalNoticeModel::factory()->reset()->findAll()->getData();
			$this->set('certainDates', $certainDates);
			
			$this->appendJs('pjAdminOptions.js');
		}
	}
	
	public function pjActionConfirmation()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				
			$this->set('arr', $arr);
			
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

            $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
			
		}
	}
	
	public function pjActionTerm()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				
			$this->set('arr', $arr);
			
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

            $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
			
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['options_update']))
			{
				$OptionModel = new pjOptionModel();
			
				foreach ($_POST as $key => $value)
				{
					if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
					{
						list(, $type, $k) = explode("-", $key);
						if (!empty($k))
						{
							$OptionModel
								->reset()
								->where('foreign_id', $this->getForeignId())
								->where('`key`', $k)
								->limit(1)
								->modifyAll(array('value' => $value));
						}
					}
				}
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], 1, 'pjOption', 'data');
				}
				
				if (isset($_POST['next_action']))
				{
					switch ($_POST['next_action'])
					{
						case 'pjActionIndex':
							$err = 'AO01';
							break;
						case 'pjActionBooking':
							$err = 'AO02';
							break;
						case 'pjActionBookingForm':
							$err = 'AO03';
							break;
						case 'pjActionConfirmation':
							$err = 'AO04&tab_id=' . $_POST['tab_id'];
							break;
						case 'pjActionTerm':
							$err = 'AO05';
							break;
					}
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] . "&err=$err");
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionInstall()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
				->orderBy('t1.sort ASC')->findAll()->getData();
			$this->set('locale_arr', $locale_arr);

            $this->set('pickup_arr', pjLocationModel::factory()
                ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->select("t1.*, t2.content as name")
                ->where('t1.status', 'T')
                ->orderBy("is_airport DESC, name ASC")
                ->findAll()->getDataPair('id', 'name')
            );

            $this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionPreview()
	{
		$this->setLayout('pjActionEmpty');
	}

    public function pjActionGetDropoff()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            $location_id = $_GET['install_location_id'];

            $this->set('dropoff_arr', pjDropoffModel::factory()
                ->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->select("t1.*, t2.content as name")
                ->where('t1.location_id', $location_id)
                ->orderBy("name ASC")
                ->findAll()->getDataPair('id', 'name')
            );
        }
    }
    
	public function pjActionExportEmails()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['export'])) {
				$date_from = (isset($_POST['date_from']) && $_POST['date_from'] != '') ? pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']) : date('Y-m-d');
				$date_to = (isset($_POST['date_to']) && $_POST['date_to'] != '') ? pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']) : date('Y-m-d');
				
				$arr = pjBookingModel::factory()->select('DISTINCT t1.c_email AS Email')
					->where('t1.booking_date BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
					->where('t1.c_email IS NOT NULL')
					->orderBy('t1.c_email ASC')
					->findAll()
					->getData();
				
				$csv = new pjCSV();
				$csv
					->setHeader(true)
					->setName("Emails-".time().".csv")
					->process($arr)
					->download();
				exit;
			}
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
}
?>