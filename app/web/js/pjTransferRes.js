/*!
 * Transfer Reservation v1.0
 * http://www.phpjabbers.com/transfer-reservation/
 * 
 * Copyright 2014, StivaSoft Ltd.
 * 
 */
(function (window, undefined){
	"use strict";

	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	var document = window.document,
        isPageLoaded = false,
		validate = (pjQ.$.fn.validate !== undefined),
		datepicker = (pjQ.$.fn.datepicker !== undefined);
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function TransferRes(opts) {
		if (!(this instanceof TransferRes)) {
			return new TransferRes(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	TransferRes.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	TransferRes.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	TransferRes.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.page = null;
			this.opts = {};
			
			return this;
		},
        pageLoaded: function () {
            // Created this method instead of using window.onload event because sometimes the event is triggered before DOM is loaded.
            // Then jQuery can't find the preloader and fails to hide it, which causes infinite "loading" screen even if the page is loaded successfully.

            // UNIFY HEIGHT
            var maxHeight = 0;

            pjQ.$('.heightfix').each(function(){
                if (pjQ.$(this).height() > maxHeight) { maxHeight = pjQ.$(this).height(); }
            });
            pjQ.$('.heightfix').height(maxHeight);

            // PRELOADER
            pjQ.$('.preloader').fadeOut();

            isPageLoaded = true;
        },
		disableButtons: function () {
			var $el;
			this.$container.find(".btn").each(function (i, el) {
				$el = pjQ.$(el).attr("disabled", "disabled");
				if ($el.hasClass("btn")) {
					$el.addClass("trButtonDisable");
				}
			});
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled").removeClass("trButtonDisable");
		},
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("trContainer_" + this.opts.index);
			this.$container = pjQ.$(this.container);

            pjQ.$.validator.setDefaults({
                onkeyup: false,
                errorElement: 'em',
                errorClass: 'trError',
                errorPlacement: function (error, element) {
                    if(element.is('select'))
                    {
                        if(element.hasClass('select2'))
                        {
                            error.insertAfter(element.next('.select2-container'));
                        }
                        else
                        {
                            error.insertAfter(element.parent());
                        }
                    }
                    else if(element.hasClass('hasDatepicker'))
                    {
                        error.insertAfter(element);
                    }
                    else if(element.is(':checkbox'))
                    {
                        error.appendTo(element.closest('.f-row'));
                    }
                    else
                    {
                        error.insertAfter(element);
                    }
                }
            });

			this.$container.on("click.tr", ".btn", function (e) {
                if(pjQ.$(this).hasClass('trButtonDisable'))
                {
                    if (e && e.preventDefault) {
                        e.preventDefault();
                    }
                }
            }).on("click.tr", ".trSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var locale = pjQ.$(this).data("id");
				self.opts.locale = locale;
				pjQ.$(this).addClass("trLocaleFocus").parent().parent().find("a.trSelectorLocale").not(this).removeClass("trLocaleFocus");
				
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale", "&session_id=", self.opts.session_id].join(""), {
					"locale_id": locale
				}).done(function (data) {
					self.loadSearch.call(self);
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("change.tr", "#trLocationId_"+ self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.getLocations();
			}).on("change.tr", "#trCountryId_"+ self.opts.index, function (e) {
                pjQ.$('#trDialingCode_' + self.opts.index).val(pjQ.$(this).find('option:selected').data('code'));
            }).on("click.tr", ".trChooseDateButton", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('html, body').animate({
                    scrollTop: pjQ.$('#trDate_' + self.opts.index).offset().top
                }, 500);
                pjQ.$('#trDate_' + self.opts.index).focus();
				return false;
			}).on("click.tr", ".trChooseVehicleButton", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
                if(pjQ.$(this).hasClass('trButtonDisable'))
                {
                    return false;
                }
                pjQ.$('.trChooseVehicleButton').removeClass('active');
                pjQ.$(this).addClass('active');
                var fleet_id = pjQ.$(this).attr('data-id'),
                    params = 	{
                        "locale": self.opts.locale,
                        "hide": self.opts.hide,
                        "index": self.opts.index,
                        "fleet_id": fleet_id
                    };
                self.disableButtons.call(self);
                pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionAddFleet", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                    self.loadTransferType.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
				return false;
			}).on("click.tr", ".tr-page-clickable", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
                self.page = pjQ.$(this).attr('rev');
                self.loadServices.call(self);
				return false;
			}).on("click.tr", ".trChooseTransferTypeButton", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                if(pjQ.$(this).hasClass('trButtonDisable'))
                {
                    return false;
                }
                pjQ.$('.trChooseTransferTypeButton').removeClass('active');
                pjQ.$(this).addClass('active');
                var params = 	{
                        "locale": self.opts.locale,
                        "hide": self.opts.hide,
                        "index": self.opts.index,
                        "is_return": pjQ.$(this).data('is-return')
                    };
                self.disableButtons.call(self);
                pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetTransferType", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                    self.loadExtras.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
                return false;
            }).on("change.tr", "#trExtrasForm_" + self.opts.index + ' select', function (e) {
                self.disableButtons.call(self);
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionUpdateExtras", "&session_id=", self.opts.session_id].join(""), pjQ.$('#trExtrasForm_' + self.opts.index).serialize()).done(function (data) {
                    pjQ.$('dd.trCartExtras').html(data.extras);
                    pjQ.$('.trCartExtras').toggle(data.extras !== undefined && data.extras !== null && data.extras.length > 0);
                    self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
            }).on("click.tr", "#trBtnExtras_" + self.opts.index, function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                self.loadDeparture.call(self);
            }).on("click.tr", "#trBtnTerms_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
                pjQ.$("#trTermContainer_" + self.opts.index).slideToggle();
			}).on("click.tr", "#trBtnSharedTrip_" + self.opts.index, function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$("#trSharedTripInfoContainer_" + self.opts.index).slideToggle();
            }).on("click.bs", "#pjTrCaptchaImage_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $captcha = pjQ.$(this);
				$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
				pjQ.$('#trCaptcha_' + self.opts.index).val("").removeData('previousValue');
			}).on("click.tr", ".trPaymentMethodSelector", function (e) {
                var $pm = pjQ.$(this).val();
                pjQ.$("#trPaymentMethod_" + self.opts.index).val($pm);
                pjQ.$("#trPaymentMethod_" + self.opts.index).trigger('change');
            }).on("change.tr", "#trPaymentMethod_" + self.opts.index, function (e) {
            	/*var $attr_pm = pjQ.$('option:selected', pjQ.$(this)).attr('data-pm');
                if(pjQ.$(this).find('option:selected').length > 0)
                {
                    pjQ.$('dd.trCartPaymentMethod_Checkout_' + self.opts.index).html(pjQ.$(this).find('option:selected').text());
                }
                pjQ.$('.trCartPaymentMethod_Checkout_' + self.opts.index).toggle(pjQ.$(this).val() != '');
                pjQ.$('.trCartDeposit_Checkout_' + self.opts.index + ', .trCartRest_Checkout_' + self.opts.index).toggle(pjQ.$(this).val() == 'creditcard');
                if (pjQ.$(this).val() == 'creditcard' || (pjQ.$(this).val() == 'saferpay' && $attr_pm == 'direct')) {
                	pjQ.$("#trCCData_" + self.opts.index).show();
                } else {
                	pjQ.$("#trCCData_" + self.opts.index).hide();
                }*/
            	
            	var $attr_pm = pjQ.$('option:selected', pjQ.$(this)).attr('data-pm'),
	        		$attr_deposit = pjQ.$('option:selected', pjQ.$(this)).attr('data-deposit'),
	        		$attr_total = pjQ.$('option:selected', pjQ.$(this)).attr('data-total'),
	        		$html_cc_fee = pjQ.$('option:selected', pjQ.$(this)).attr('data-html_cc_fee');
	            if(pjQ.$(this).find('option:selected').length > 0)
	            {
	            	if ($html_cc_fee.length > 0) {
	            		pjQ.$('.pjSbCartPaymentMethod').html(pjQ.$(this).find('option:selected').text() + '<br/>' + $html_cc_fee);
	            	} else {
	            		pjQ.$('.pjSbCartPaymentMethod').html(pjQ.$(this).find('option:selected').text());
	            	}
	            }
	            if (pjQ.$(this).val() == 'creditcard' || (pjQ.$(this).val() == 'saferpay' && $attr_pm == 'direct')) {
	            	pjQ.$("#trCCData_" + self.opts.index + ', .trCartDeposit_Checkout_' + self.opts.index + ', .trCartRest_Checkout_' + self.opts.index).show();
	            	pjQ.$("#trCCData_" + self.opts.index).find('input.form-control').addClass('required');
	            } else {
	            	pjQ.$("#trCCData_" + self.opts.index + ', .trCartDeposit_Checkout_' + self.opts.index + ', .trCartRest_Checkout_' + self.opts.index).hide();
	            	pjQ.$("#trCCData_" + self.opts.index).find('input.form-control').removeClass('required');
	            }
	            if (pjQ.$(this).val() == 'creditcard' || pjQ.$(this).val() == 'saferpay') {
	            	pjQ.$('.pjSbFullPriceChargedDesc').show();
	            } else {
	            	pjQ.$('.pjSbFullPriceChargedDesc').hide();
	            }
	            
	            pjQ.$('.pjSbCartDeposit').html($attr_deposit);
	            pjQ.$('.pjSbCartTotal').html($attr_total);
                
                var $html_book = pjQ.$('.btnBook').attr('data-html_book'),
	            	$html_book_pay = pjQ.$('.btnBook').attr('data-html_book_pay');
	            if (pjQ.$(this).val() == 'saferpay') {
	            	pjQ.$('.btnBook').html($html_book_pay);
	            	pjQ.$('.btnFinishBooking').hide();
	            } else {
	            	pjQ.$('.btnBook').html($html_book);
	            	pjQ.$('.btnFinishBooking').show();
	            }
            }).on("change.tr", "#voucher_code", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var voucher_code = pjQ.$(this).val(),
                    params = 	{
                        "locale": self.opts.locale,
                        "hide": self.opts.hide,
                        "index": self.opts.index,
                        "voucher_code": voucher_code
                    };
                self.disableButtons.call(self);

                pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionApplyCode", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                    switch (parseInt(data.code, 10)) {
                        case 200:
                            pjQ.$('dd.trCartDiscount_Checkout_' + self.opts.index).html(data.discount_print);
                            pjQ.$('.trCartDiscount_Checkout_' + self.opts.index).show();

                            if(pjQ.$('.trCartDiscount_Checkout_' + self.opts.index).length > 0)
                            {
                                pjQ.$('dd.trCartDeposit_Checkout_' + self.opts.index).html(data.deposit);
                                pjQ.$('dd.trCartRest_Checkout_' + self.opts.index).html(data.rest);
                                pjQ.$('.trCartDeposit_Checkout_' + self.opts.index + ', .trCartRest_Checkout_' + self.opts.index).toggle(pjQ.$('#trPaymentMethod_' + self.opts.index).val() == 'creditcard');
                            }

                            pjQ.$('#trCart_Checkout_' + self.opts.index).find('.total > dd').html(data.total);
                            break;
                        default:
                            pjQ.$('.trCartDiscount_Checkout_' + self.opts.index).hide();

                            if(pjQ.$('.trCartDiscount_Checkout_' + self.opts.index).length > 0)
                            {
                                pjQ.$('dd.trCartDeposit_Checkout_' + self.opts.index).html(data.deposit);
                                pjQ.$('dd.trCartRest_Checkout_' + self.opts.index).html(data.rest);
                                pjQ.$('.trCartDeposit_Checkout_' + self.opts.index + ', .trCartRest_Checkout_' + self.opts.index).toggle(pjQ.$('#trPaymentMethod_' + self.opts.index).val() == 'creditcard');
                            }

                            pjQ.$('#trCart_Checkout_' + self.opts.index).find('.total > dd').html(data.total);

                            if(data.text !== undefined && data.text !== null && data.text.length > 0)
                            {
                                var validator = pjQ.$('#trCheckoutForm_' + self.opts.index).validate(); // get instance
                                validator.showErrors({ voucher_code: data.text });
                            }
                    }
                    self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
                return false;
            }).on("change.tr", '#time_h, #time_m', function (e) {
                var $form = pjQ.$(this).closest('form'),
                	time = '',
                    hours = pjQ.$('#time_h').val(),
                    minutes = pjQ.$('#time_m').val();

                if(hours.length > 0 && minutes.length > 0)
                {
                    hours = ('0' + hours).substr(hours.length - 1);
                    minutes = ('0' + minutes).substr(minutes.length - 1);
                    time = hours + ':' + minutes;
                }

                pjQ.$('dd.trCartDepartureTime').html(time);
                pjQ.$('.trCartDepartureTime').toggle(time.length > 0);
                
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	self.loadDeparture.call(self);
                	self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
            }).on("change.tr", '#return_time_h, #return_time_m', function (e) {
                var $form = pjQ.$(this).closest('form'),
                	time = '',
                    hours = pjQ.$('#return_time_h').val(),
                    minutes = pjQ.$('#return_time_m').val();

                if(hours.length > 0 && minutes.length > 0)
                {
                    hours = ('0' + hours).substr(hours.length - 1);
                    minutes = ('0' + minutes).substr(minutes.length - 1);
                    time = hours + ':' + minutes;
                }

                pjQ.$('dd.trCartReturnTime').html(time);
                pjQ.$('.trCartReturnTime').toggle(time.length > 0);
                
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	self.loadReturn.call(self);
                	self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
            }).on("change.tr", '#passengers', function (e) {
                var pax = pjQ.$(this).val();
                pjQ.$('dd.trCartPax').html(pax);
                pjQ.$('.trCartPax').toggle(pax.length > 0);
            });

            pjQ.$(document).on("click", ".trigger", function (e) {
            	var $obj = pjQ.$(this).closest('.result').find('.information');
            	$obj.slideToggle(500, 
            		function(){ 
	            		if (pjQ.$(window).width() < 981) {
	            			if (!pjQ.$(this).is(':hidden')) {
	            				pjQ.$('html, body').animate({
	                                scrollTop: $obj.offset().top
	                            }, 500);
	            			}
	            		}
            		}
            	)
            }).on("click", ".close", function (e) {
                pjQ.$(this).closest('.information').hide(500);
            }).on("select2:open", function() {
                pjQ.$(".select2-search--dropdown .select2-search__field").attr("placeholder", self.opts.search_placeholder);
            }).on("change.tr", ".trLoadPrices", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $form = pjQ.$(this).closest('form'), 
					$location_id = pjQ.$("#trLocationId_"+ self.opts.index).val(),
	        		$dropoff_id = pjQ.$("#trDropoffId_"+ self.opts.index).val(),
	        		$date = pjQ.$("#trDate_"+ self.opts.index).val();
	        	if (parseInt($location_id) > 0 && parseInt($dropoff_id) > 0 && $date !== '') {
	        		$form.trigger('submit');
	        	}
				return false;
			}).on("click.tr", ".btnFinishBooking", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var $form = pjQ.$('#trPaymentForm_' + self.opts.index);
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionFinishBooking", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	self.loadSummary.call(self, data.booking_id);
                });
            });

            if (self.opts.load_summary == 1) {
            	self.loadSummary.call(self, self.opts.booking_id);
            } else if (self.opts.load_payment == 1) {
            	self.loadPayment.call(self, self.opts.booking_uuid);
            } else {
            	self.loadSearch.call(self);
            }
		},
		loadSearch: function() {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionSearch", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.html(data);
				if(pjQ.$('#trReturnOn_' + index).is(':checked'))
				{
					pjQ.$('.trReturnField').prop('disabled', false);
				}else{
					pjQ.$('.trReturnField').prop('disabled', 'disabled');
				}

                self.bindSearch.call(self);
                if(parseInt(pjQ.$('#autoloadNextStep_' + index).val(), 10) == 1)
                {
                    // pjQ.$('html, body').animate({
                    //     scrollTop: self.$container.offset().top
                    // }, 0);

                    self.loadServices.call(self);
                }
                else
                {
					/*
                    pjQ.$('html, body').animate({
                        scrollTop: self.$container.offset().top
                    }, 500);
					*/
                    self.pageLoaded.call(self);
                }
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		bindSearch: function(){
			var self = this;
            var $form = pjQ.$('#trSearchForm_' + self.opts.index);

            if($form.find("#trDate_" + self.opts.index).length > 0)
            {
                pjQ.$('#trDate_' + self.opts.index).datepicker({
                    showMillisec: false,
                    showMicrosec: false,
                    showTimezone: false,
                    showTime: false,
                    showHour: false,
                    showMinute: false,
                    numberOfMonths: 1,
                    minDate: new Date(),
                    firstDay: self.opts.startDay,
                    dateFormat: self.opts.jsDateFormat,
                    dayNames: self.opts.dayNames,
                    monthNames: self.opts.monthNamesFull,
                    addSliderAccess: true,
                    sliderAccessArgs: { touchonly: false },
                    onSelect: function() {
                    	var $location_id = pjQ.$("#trLocationId_"+ self.opts.index).val(),
                    		$dropoff_id = pjQ.$("#trDropoffId_"+ self.opts.index).val();
                    	if (parseInt($location_id) > 0 && parseInt($dropoff_id) > 0) {
                    		$form.trigger('submit');
                    	}
				    }
                }).on("change", function (e) {
                    pjQ.$(this).valid();
                });
                pjQ.$('.ui-datepicker').addClass('notranslate');
            }

            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();
            $form.find("select.select2").select2({
                width: "100%",
                templateResult: function (state) {
                    return pjQ.$('<span><i class="' + pjQ.$(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
                },
                "language": {
                    "noResults": function(){
                        return pjQ.$('<a href="' + self.opts.url.send_inquiry + '">' + self.opts.i18n.send_inquiry + '</a>');
                    }
                }
            });

            $form.validate({
                submitHandler: function (form) {
                    self.disableButtons.call(self);
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheck", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        switch (parseInt(data.code, 10)) {
                            case 100:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.no_fleet).show().fadeOut(3000);
                                self.enableButtons.call(self);
                                break;
                            case 101:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.invalid_date).show().fadeOut(5000);
                                self.enableButtons.call(self);
                                break;
                            case 200:
                                self.page = 1;
                                self.loadServices.call(self);
                                break;
                        }
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
            });

            if ($form.find('[data-skip-first-step]').length > 0) {
                $form.trigger('submit');
            }
		},
		loadServices: function () {
			var self = this,
				index = this.opts.index,
                params = 	{
                                "locale": this.opts.locale,
                                "hide": this.opts.hide,
                                "index": this.opts.index,
                                "page": this.page,
                            };
            var $container = pjQ.$('#trBookingStep_Services');
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionServices", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);
                self.enableButtons.call(self);

                if(isPageLoaded)
                {
                    pjQ.$('html, body').animate({
                        scrollTop: $container.offset().top
                    }, 500);
                }
                else
                {
                    self.pageLoaded.call(self);
                }
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		bindCheckout: function () {
			var self = this;
			var $form = pjQ.$('#trCheckoutForm_' + self.opts.index);
			
			pjQ.$('.payment-methods .payment-method').each(function() {
				var $h = pjQ.$(this).find('.payment-method-info').height();
				pjQ.$(this).find('.radio').css('top', ($h/2)-20 + 'px');
			});
			
			pjQ.$('#trPaymentMethod_' + self.opts.index).trigger('change');
			
			pjQ.$(window).resize(function(){
				pjQ.$('.payment-methods .payment-method').each(function() {
					var $h = pjQ.$(this).find('.payment-method-info').height();
					pjQ.$(this).find('.radio').css('top', ($h/2)-20 + 'px');
				});
			});
			
			$form.validate({
				submitHandler: function (form) {
                    // self.disableButtons.call(self);
                    var $msg_container = pjQ.$('#trBookingMsg_' + self.opts.index);
                    $msg_container.find('.alert').removeClass('alert-danger').addClass('alert-success').text(self.opts.message_0);
                    $msg_container.css('display', 'block');

                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveBooking", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        if (!data.code) {
                            return;
                        }
                        switch (parseInt(data.code, 10)) {
                            case 100:
                                $msg_container.find('.alert').removeClass('alert-success').addClass('alert-danger').text(self.opts.message_4);
                                self.enableButtons.call(self);
                                break;
                            case 101:
                                $msg_container.find('.alert').removeClass('alert-success').addClass('alert-danger').text(data.text);
                                self.enableButtons.call(self);
                                break;
                            case 200:
                            	if (data.payment_method == 'saferpay') {
                            		self.loadPayment.call(self, data.booking_uuid);
                            	} else {
                            		self.loadSummary.call(self, data.booking_id);
                            	}                                
                                break;
                        }
                    });
					return false;
				}
			});
		},
        loadTransferType: function () {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_TransferType');
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionTransferType", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                $container.find('input[type=radio]').uniform();

                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        loadExtras: function () {
            var self = this,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Extras');
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionExtras", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                $container.find('select:not(.select2)').uniform();

                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        loadDeparture: function () {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Departure');
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionDeparture", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                self.bindDeparture.call(self);
                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        bindDeparture: function(){
            var self = this;
            var $form = pjQ.$('#trDepartureForm_' + self.opts.index);

            if($form.find("#trDateConfirm_" + self.opts.index).length > 0)
            {
                pjQ.$('#trDateConfirm_' + self.opts.index).datepicker({
                    showMillisec: false,
                    showMicrosec: false,
                    showTimezone: false,
                    showTime: false,
                    showHour: false,
                    showMinute: false,
                    numberOfMonths: 1,
                    minDate: new Date(),
                    firstDay: self.opts.startDay,
                    dateFormat: self.opts.jsDateFormat,
                    dayNames: self.opts.dayNames,
                    monthNames: self.opts.monthNamesFull,
                    addSliderAccess: true,
                    sliderAccessArgs: { touchonly: false }
                }).on("change", function (e) {
                    pjQ.$(this).valid();
                    pjQ.$('dd.trCartDepartureDate').html(e.target.value);
                    pjQ.$('.trCartDepartureDate').toggle(e.target.value.length > 0);
                    pjQ.$('#trDateConfirmMsg_' + self.opts.index).toggle(e.target.value != pjQ.$('#trDateOriginal_' + self.opts.index).val());
                    
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                    	self.loadDeparture.call(self);
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                });
                pjQ.$('.ui-datepicker').addClass('notranslate');
            }

            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();

            $form.validate({
                submitHandler: function (form) {
                    self.disableButtons.call(self);
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        switch (parseInt(data.code, 10)) {
                            case 200:
                                if(data.is_return == 1)
                                {
                                    self.loadReturn.call(self);
                                }
                                else
                                {
                                    self.loadPassenger.call(self);
                                }
                                break;
                            default:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.generic_error).show().fadeOut(3000);
                                self.enableButtons.call(self);
                        }
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
            });
        },
        loadReturn: function () {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Return');
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionReturn", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                self.bindReturn.call(self);
                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        bindReturn: function(){
            var self = this;
            var $form = pjQ.$('#trReturnForm_' + self.opts.index);

            if($form.find("#trReturnDate_" + self.opts.index).length > 0)
            {
                pjQ.$('#trReturnDate_' + self.opts.index).datepicker({
                    showMillisec: false,
                    showMicrosec: false,
                    showTimezone: false,
                    showTime: false,
                    showHour: false,
                    showMinute: false,
                    numberOfMonths: 1,
                    minDate: pjQ.$("#trReturnDate_" + self.opts.index).data("min"),
                    firstDay: self.opts.startDay,
                    dateFormat: self.opts.jsDateFormat,
                    dayNames: self.opts.dayNames,
                    monthNames: self.opts.monthNamesFull,
                    addSliderAccess: true,
                    sliderAccessArgs: { touchonly: false }
                }).on("change", function (e) {
                    pjQ.$(this).valid();
                    pjQ.$('dd.trCartReturnDate').html(e.target.value);
                    pjQ.$('.trCartReturnDate').toggle(e.target.value.length > 0);
                    
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                    	self.loadReturn.call(self);
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                });
                pjQ.$('.ui-datepicker').addClass('notranslate');
            }

            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();

            $form.validate({
                submitHandler: function (form) {
                    self.disableButtons.call(self);
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        switch (parseInt(data.code, 10)) {
                            case 200:
                                self.loadPassenger.call(self);
                                break;
                            default:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.generic_error).show().fadeOut(3000);
                                self.enableButtons.call(self);
                        }
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
            });
        },
        loadPassenger: function () {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Passenger');
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionPassenger", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                self.bindPassenger.call(self);
                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        bindPassenger: function(){
            var self = this;
            var $form = pjQ.$('#trPassengerForm_' + self.opts.index);

            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();
            $form.find('select.select2').select2({ width: "100%" });

            $form.validate({
                rules: {
                    "email2":     {
                        required: true,
                        email: true,
                        equalTo: "#email"
                    }
                },
                submitHandler: function (form) {
                    self.disableButtons.call(self);
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSavePassenger", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        switch (parseInt(data.code, 10)) {
                            case 200:
                                self.loadCheckout.call(self);
                                break;
                            default:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.generic_error).show().fadeOut(3000);
                                self.enableButtons.call(self);
                        }
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
            });
        },
        loadCheckout: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};

            var $container = pjQ.$('#trBookingStep_Checkout');
            $container.nextAll('[id^="trBookingStep_"]').empty();

			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCheckout", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                $container.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();

				pjQ.$('html, body').animate({
			        scrollTop: $container.offset().top
			    }, 500);
				self.bindCheckout.call(self);
                self.enableButtons.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadSummary: function (booking_id) {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index,
                                "booking_id": booking_id
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionSummary", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.replaceWith(data);
                pjQ.$('html, body').animate({
                    scrollTop: pjQ.$('main.main').offset().top
                }, 500);
                
                var $msg_container = pjQ.$('#trBookingMsg_' + index);
                if ($msg_container.find("form[name='trPaypal']").length > 0) {
                	setTimeout(function() {
                		$msg_container.find("form[name='trPaypal']").trigger('submit');
	        		}, 3000);
                } else if($msg_container.find("form[name='trAuthorize']").length > 0) {
                	setTimeout(function() {
                		$msg_container.find("form[name='trAuthorize']").trigger('submit');
	        		}, 3000);                	
                } else if($msg_container.find("iframe[name='trSaferpay']").length > 0) {
                	setTimeout(function() {
    		            pjQ.$(window).bind("message", function (e) {
    		            	 pjQ.$("#trSaferpay_" + self.opts.index).css("height", e.originalEvent.data.height + "px");
    		            });
    		            pjQ.$('html, body').animate({
    	                    scrollTop: pjQ.$('#trSaferpayForm_' + self.opts.index).offset().top
    	                }, 500);
	        		}, 3000);
                }
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadPayment: function (booking_uuid) {
            var self = this,
                index = this.opts.index,
                params = 	{
					"locale": this.opts.locale,
					"hide": this.opts.hide,
					"index": this.opts.index,
					"booking_uuid": booking_uuid
				};
            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionPayment", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
            	self.$container.html(data);
            	pjQ.$('html, body').animate({
                    scrollTop: pjQ.$('main.main').offset().top
                }, 500);
                
                var $form = pjQ.$('#trPaymentForm_' + self.opts.index);
                $form.find('input[type=radio]').uniform();
                
                pjQ.$(window).bind("message", function (e) {
                	if (e.originalEvent.data.height <= 450) return;
                	pjQ.$("#trSaferpay_" + index).css("height", e.originalEvent.data.height + "px");
                });
                
                pjQ.$('.payment-methods .payment-method').each(function() {
    				var $h = pjQ.$(this).find('.payment-method-info').height();
    				pjQ.$(this).find('.radio').css('top', ($h/2)-20 + 'px');
    			});
    			
    			pjQ.$(window).resize(function(){
    				pjQ.$('.payment-methods .payment-method').each(function() {
    					var $h = pjQ.$(this).find('.payment-method-info').height();
    					pjQ.$(this).find('.radio').css('top', ($h/2)-20 + 'px');
    				});
    			});
                
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
		getLocations: function(){
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index,
								"location_id" : pjQ.$("#trLocationId_"+ index).val(),
								"date" : pjQ.$("#trDate_"+ index).val()
							};
			self.disableButtons();
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetLocations", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				pjQ.$('#dropoffBox_' + index).html(data);
                pjQ.$('#dropoffBox_' + index + ' select').select2({
                    width: "100%",
                    templateResult: function (state) {
                        return pjQ.$('<span><i class="' + pjQ.$(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
                    },
                    "language": {
                        "noResults": function(){
                            return pjQ.$('<a href="' + self.opts.url.send_inquiry + '">' + self.opts.i18n.send_inquiry + '</a>');
                        }
                    }
                });
                self.enableButtons.call(self);
                
                var $form = pjQ.$("#trSearchForm_"+ self.opts.index), 
					$location_id = pjQ.$("#trLocationId_"+ self.opts.index).val(),
	        		$dropoff_id = pjQ.$("#trDropoffId_"+ self.opts.index).val(),
	        		$date = pjQ.$("#trDate_"+ self.opts.index).val();
	        	if (parseInt($location_id) > 0 && parseInt($dropoff_id) > 0 && $date !== '') {
	        		$form.trigger('submit');
	        	}
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index;
			var qs = {
					"cid": this.opts.cid,
					"locale": this.opts.locale,
					"hide": this.opts.hide,
					"index": this.opts.index,
					"booking_id": obj.booking_id, 
					"payment_method": obj.payment
				};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetPaymentForm", "&session_id=", self.opts.session_id].join(""), qs).done(function (data) {
				var $msg_container = pjQ.$('#trBookingMsg_' + index);
				$msg_container.html(data);
				$msg_container.parent().css('display', 'block');
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='trPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='trAuthorize']").trigger('submit');
						break;
					case 'saferpay':
						pjQ.$('.modal-dialog').css("z-index", "9999");
			            pjQ.$('#modalSaferpay_' + self.opts.index).modal('show');
			            pjQ.$(window).bind("message", function (e) {
			            	 pjQ.$("#iframeSaferpay_" + self.opts.index).css("height", e.originalEvent.data.height + "px");
			            });
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		}
	};
	
	window.TransferRes = TransferRes;	
})(window);