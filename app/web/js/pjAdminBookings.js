var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var 
			$frmCreateBooking = $('#frmCreateBooking'),
			$frmUpdateBooking = $('#frmUpdateBooking'),
			$dialogSelect = $("#dialogSelect"),
			$dialogDeleteNameSign = $("#dialogDeleteNameSign"),
			$dialogSendWhatsappMessage = $("#dialogSendWhatsappMessage"),
			dialog = ($.fn.dialog !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			spinner = ($.fn.spinner !== undefined),
			chosen = ($.fn.chosen !== undefined),
			tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			},
			validate = ($.fn.validate !== undefined);
	
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 0,
			spin: function(event, ui) {
		        if (this.name == 'passengers') {
		        	var $has_return = $('#has_return').is(":checked"),
		        		$passengers = parseInt($('#fleet_id').find(':selected').attr('data-passengers'), 10);
		        	if ($has_return) {
		        		var $cnt = ui.value;
		        		if ($cnt > $passengers) {
		        			$cnt = $passengers;
		        		}
		        		$('#passengers_return').val($cnt);
		        	}
		        }
		    }
		});
		if (chosen) {
			$("#location_id").chosen();
			$("#dropoff_id").chosen();
			$("#pickup_id").chosen();
			$("#search_dropoff_id").chosen();
			$("#fleet_id").chosen();
			$("#c_country").chosen();
			$("#client_id").chosen();
			$("#driver_id").chosen();
		}
		
		if ($dialogDeleteNameSign.length > 0 && dialog) {
			$dialogDeleteNameSign.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				width: 350,
				buttons: (function () {
					var buttons = {};
					buttons[myLabel.btnDelete] = function () {
						$.get("index.php?controller=pjAdminBookings&action=pjActionDeleteFile&id=" + $dialogDeleteNameSign.data('id')).done(function (data) {
							$('.deleteNameSignWrap').remove();
							$dialogDeleteNameSign.dialog("close");
						});
					};
					buttons[myLabel.btnCancel] = function () {
						$dialogDeleteNameSign.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		if ($frmCreateBooking.length > 0 || $frmUpdateBooking.length > 0) 
		{
			$.validator.addMethod('positiveNumber', function (value) { 
				return Number(value) >= 0;
			}, myLabel.positive_number);
			
			$.validator.addMethod('maximumNumber', function (value, element) { 
				var data = parseInt($(element).attr('data-value'), 10);
				if(Number(value) > data)
				{
					return false;
				}else{
					return true;
				}
			}, myLabel.max_number);
			
			$frmCreateBooking.validate({
				rules: {
					passengers: {
						positiveNumber: true,
						maximumNumber: true
					},
					passengers_return: {
						positiveNumber: true,
						maximumNumber: true
					},
					luggage: {
						positiveNumber: true,
						maximumNumber: true
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'booking_date' || element.attr('name') == 'passengers' || element.attr('name') == 'passengers_return' || element.attr('name') == 'luggage')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ":hidden",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				}
			});
			$frmUpdateBooking.validate({
				rules:{
					"return_date":{
						required: function(){
							return $('#has_return').is(':checked');
						}
					},
					uuid: {
						required: true,
						remote: "index.php?controller=pjAdminBookings&action=pjActionCheckID&id=" + $frmUpdateBooking.find("input[name='id']").val()
					},
					passengers: {
						positiveNumber: true,
						maximumNumber: true
					},
					passengers_return: {
						positiveNumber: true,
						maximumNumber: true
					},
					luggage: {
						positiveNumber: true,
						maximumNumber: true
					}
				},
				messages:{
					uuid: {
						remote: myLabel.duplicated_id
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'return_date' || element.attr('name') == 'passengers' || element.attr('name') == 'passengers_return' || element.attr('name') == 'luggage')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
                ignore: ":hidden",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				}
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:bid}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:bid}"}
						  ],
				columns: [
				          {text: myLabel.client, type: "text", sortable: false, width:180},
				          {text: myLabel.transfer_date_time, type: "text", sortable: true, width:160},
				          {text: myLabel.transfer_destinations, type: "text", sortable: false, width:160},
				          {text: myLabel.fleet, type: "text", sortable: true, width:220},
				          {text: myLabel.passengers, type: "text", sortable: true, width:100, align: "center"},
				          {text: myLabel.extras, type: "text", sortable: false, width:160},
				          {text: myLabel.payment_method, type: "text", sortable: false, width:160},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 110, options: [
				                                                                                     {label: myLabel.pending, value: "pending"},
				                                                                                     {label: myLabel.in_progress, value: "in_progress"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['client', 'booking_date', 'pickup_dropoff', 'fleet', 'passengers', 'extras', 'payment_method', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", render: false, ajax: false},
					   {text: myLabel.print, url: "javascript:void(0);", render: false},
					   {text: myLabel.print_reservation_details, url: "javascript:void(0);", render: false},
					   {text: myLabel.print_reservation_details_single, url: "javascript:void(0);", render: false},
					   {text: myLabel.remind_client_for_return_via_email, url: "javascript:void(0);", render: false},
					   {text: myLabel.remind_client_via_email, url: "javascript:void(0);", render: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				},
				onRender: function() {
					var $content = $grid.datagrid("option", "content").data;
					$.each($content, function(index, item) {
					  var $row = $('[data-id="id_'+item.id+'"]');
					  if (item.booking_color != '') {
						  $row.find('td:first-child').css('border-left', '5px solid '+item.booking_color);
					  }
					  if (item.double_bookings > 0) {
						  $row.find('td').css('background', '#fce8cd');
					  }
					});
				}
			});
		}
		
		$(document).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev"),
				onSelect: function (dateText, inst) {
					if($this.attr('name') == 'booking_date' || $this.attr('name') == 'return_date')
                    {
						if ($frmCreateBooking.length) {
							calPrice($frmCreateBooking);
						}
                    }
				}
			});
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				if(!$dp.is('[disabled=disabled]'))
				{
					$dp.trigger("focusin").datepicker("show");
				}
			}
		}).on("focusin", ".datetimepick", function (e) {
			var minDateTime, maxDateTime,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					timeFormat: $this.attr("lang"),
					stepMinute: 5,
                    onSelect: function (dateText, inst) {
                        if($this.attr('name') == 'booking_date' || $this.attr('name') == 'return_date')
                        {
							if ($frmCreateBooking.length) {
								calPrice($frmCreateBooking);
							}
                        }
                    }
			    };
			switch ($this.attr("name")) 
			{
				case "booking_date":
					if($(".datetimepick[name='return_date']").val() != '')
					{
						maxDateTime = $(".datetimepick[name='return_date']").datetimepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev"),
							timeFormat: $this.attr("lang")
						}).datetimepicker("getDate");
						$(".datetimepick[name='return_date']").datepicker("destroy").removeAttr("id");
						if (maxDateTime !== null) {
							custom.maxDateTime = maxDateTime;
						}
					}
					break;
				case "return_date":
					if($(".datetimepick[name='booking_date']").val() != '')
					{
						minDateTime = $(".datetimepick[name='booking_date']").datetimepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev"),
							timeFormat: $this.attr("lang")
						}).datetimepicker("getDate");
						$(".datetimepick[name='booking_date']").datepicker("destroy").removeAttr("id");
						if (minDateTime !== null) {
							custom.minDateTime = minDateTime;
						}
					}
					break;
			}
			if($('#has_return').length)
			{			
				$(this).datetimepicker($.extend(o, custom));
			}else{
				$(this).datetimepicker(o);
			}
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(".pj-button-detailed").trigger("click");
			if (chosen) {
				$("#pickup_id").val('').trigger("liszt:updated");
				$("#search_dropoff_id").val('').trigger("liszt:updated");
			}
			$('#date').val('');
			$('#email').val('');
			$('#name').val('');
			$('#phone').val('');
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				date: "",
				dropoff_id: "",
				location_id: "",
				name: "",
				phone: "",
				email: "",
				notes_for_support: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				date: "",
				dropoff_id: "",
				location_id: "",
				name: "",
				phone: "",
				email: "",
				notes_for_support: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj.notes_for_support = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter-notes-for-support", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj.notes_for_support = 1;
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				date: "",
				dropoff_id: "",
				location_id: "",
				name: "",
				phone: "",
				email: "",
				notes_for_support: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("change", "#payment_method", function (e) {
			switch ($("option:selected", this).val()) {
				case 'creditcard':
					$(".boxCC").show();
					break;
				default:
					$(".boxCC").hide();
			}

			/*if ($("option:selected", this).val() == 'creditcard' || $("option:selected", this).val() == 'bank') {
				if ($frmUpdateBooking.length > 0) {
					$frmUpdateBooking.find('#deposit').val($frmUpdateBooking.find('#total').val());
				}
				else {
					calPrice();
				}
			}
			else if ($("option:selected", this).val() == 'cash' || $("option:selected", this).val() == 'creditcard_later') {
				$frmUpdateBooking.find('#deposit').val(0);
			}*/
			var $form = $(this).closest('form');
			calPrice($form);
		}).on("change", "#location_id", function (e) {
			$.get("index.php?controller=pjAdminBookings&action=pjActionGetDropoff", {
				location_id: $(this).val()
			}).done(function (data) {
				$("#trDropoffContainer").html(data);
				$("#dropoff_id").chosen();

                var is_airport = parseInt($('#location_id').find(':selected').attr('data-is-airport'), 10);
                $('#departure_info_is_airport_0').hide();
                $('#departure_info_is_airport_1').hide();
                $('#departure_info_is_airport_2').hide();
                
                $('#return_info_is_airport_0').hide();
                $('#return_info_is_airport_1').hide();
                $('#return_info_is_airport_2').hide();
			});
		}).on("change", "#dropoff_id", function (e) {
			var $form = $(this).closest('form');
			calPrice($form);

			var duration = $('#dropoff_id').find(':selected').attr('data-duration'),
				distance = $('#dropoff_id').find(':selected').attr('data-distance'),
				pickup_airport = parseInt($('#location_id').find(':selected').attr('data-is-airport'), 10),
				dropoff_airport = parseInt($('#dropoff_id').find(':selected').attr('data-is-airport'), 10);
			$('#tr_duration').html(duration);
			$('#tr_distance').html(distance);
			$('#tr_duration').parent().css('display', 'block');
			$('#tr_distance').parent().css('display', 'block');
			
			$('#departure_info_is_airport_0').hide();
            $('#departure_info_is_airport_1').hide();
            $('#departure_info_is_airport_2').hide();
            
            $('#return_info_is_airport_0').hide();
            $('#return_info_is_airport_1').hide();
            $('#return_info_is_airport_2').hide();
            
            $('.pjHotelName').hide();
			if (pickup_airport == 0 && dropoff_airport == 0) {
				$('#departure_info_is_airport_2').show();
				$('#return_info_is_airport_2').show();
			} else if (pickup_airport == 1 && dropoff_airport == 0) {
				$('#departure_info_is_airport_1').show();
				$('#return_info_is_airport_1').show();
				$('.pjHotelName').show();
			} else {
				$('#departure_info_is_airport_0').show();
				$('#return_info_is_airport_0').show();
				$('.pjHotelName').show();
			}			
		}).on("change", "#fleet_id", function (e) {
			var $form = $(this).closest('form');
			calPrice($form);

			var passengers = parseInt($('#fleet_id').find(':selected').attr('data-passengers'), 10),
				luggage = parseInt($('#fleet_id').find(':selected').attr('data-luggage'), 10),
				curr_passengers = parseInt($('#passengers').val(),10),
				curr_passengers_return = parseInt($('#passengers_return').val(),10),
				curr_luggage = parseInt($("#luggage").val(), 10);
			if(passengers > 0)
			{
				$('#tr_max_passengers').html("("+myLabel.maximum+" "+passengers+")");
				$( "#passengers" ).spinner( "option", "max", passengers);
				if(curr_passengers > passengers)
				{
					$( "#passengers" ).val("");
				}
				$( "#passengers" ).attr('data-value', passengers);
				
				$('#tr_max_passengers_return').html("("+myLabel.maximum+" "+passengers+")");
				$( "#passengers_return" ).spinner( "option", "max", passengers);
				if(curr_passengers_return > passengers)
				{
					$( "#passengers_return" ).val("");
				}
				$( "#passengers_return" ).attr('data-value', passengers);
			}
			if(luggage > 0)
			{
				$('#tr_max_luggage').html("("+myLabel.maximum+" "+luggage+")");
				$( "#luggage").spinner( "option", "max", luggage);
				if(curr_luggage > luggage)
				{
					$( "#luggage").val("");
				}
				$( "#luggage" ).attr('data-value', luggage);
			}
		}).on("click", "#has_return", function (e) {
			if($(this).is(':checked'))
			{
				$("#return_date_outer").show();
				$('.trReturnDetails').show();
				
				$('.pjPriceRoundtrip').show();
				$('.pjPriceOneway').hide();
			}else{
				$("input[name='return_date']").val("");
				$("#return_date_outer").hide(); 
				$('.trReturnDetails').hide();
				
				$('.pjPriceRoundtrip').hide();
				$('.pjPriceOneway').show();
			}

			var $form = $(this).closest('form');
			calPrice($form);
		}).on("change", "#voucher_code", function (e) {
			var $form = $(this).closest('form');
			calPrice($form);
        }).on("change", "#client_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if($(this).val() != '')
			{
				$('#pjFdEditClient').css('display', 'block');
				var href = $('#pjFdEditClient').attr('data-href');
				href = href.replace("{ID}", $(this).val());
				$('#pjFdEditClient').attr('href', href);
				$('#pjSbNewClientWrapper').hide();
				$('#pjSbNewClientWrapper').find('.clientRequired').removeClass('required');
			}else{
				$('#pjFdEditClient').css('display', 'none');
				$('#pjSbNewClientWrapper').show();
				$('#pjSbNewClientWrapper').find('.clientRequired').addClass('required');
				$('#pjSbNewClientWrapper').find('input').val("");
				$('#pjSbNewClientWrapper').find('select').val("");
			}
		}).on("change", "#c_country", function (e) {
            $('#c_dialing_code').val($(this).find('option:selected').data('code'));
        }).on("click", ".pjMLDeleteNameSign", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDeleteNameSign.data('id', $(this).attr('data-id')).dialog('open');
			return false;
		}).on("click", ".btnCreateInvoice", function () {
			$("#frmCreateInvoice").trigger("submit");
		}).on("click", ".action-link-send-whatsapp", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $href = $(this).attr('data-href');
			$.get($href, function(data) {
				$('#dialogSendWhatsappMessageContent').html(data);
				$dialogSendWhatsappMessage.dialog('open');
			});
			
			return false;
		});

		$("#grid").on("click", 'ul.pj-menu-list li:nth-child(3) a', function (e) {
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				window.open('index.php?controller=pjAdminBookings&action=pjActionPrint&record=' + booking_id,'_blank');
			}	
			return false;
		}).on("click", 'ul.pj-menu-list li:nth-child(4) a', function (e) {
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				window.open('index.php?controller=pjAdminBookings&action=pjActionPrint&details&record=' + booking_id,'_blank');
			}	
			return false;
		}).on("click", 'ul.pj-menu-list li:nth-child(5) a', function (e) {
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				window.open('index.php?controller=pjAdminBookings&action=pjActionPrintSingle&details&record=' + booking_id,'_blank');
			}
			return false;
		}).on("click", 'ul.pj-menu-list li:nth-child(6) a', function (e) {
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				$('.pj-menu-list-wrap').hide();
				$.post('index.php?controller=pjAdminBookings&action=pjActionEmailReturnReminderBulk', {records: booking_id}).done(function (data) {
				});
			}
			return false;
		}).on("click", 'ul.pj-menu-list li:nth-child(7) a', function (e) {
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				$('.pj-menu-list-wrap').hide();
				$.post('index.php?controller=pjAdminBookings&action=pjActionEmailReminderBulk', {records: booking_id}).done(function (data) {
				});
			}
			return false;
		});
		
		function calPrice($form)
		{
			setTimeout(function() {
	            if($('#dropoff_id').val() != '' && $('#fleet_id').val() != '' && $("input[name='booking_date']").val() != '')
	            {
	                /*var opts = {
	                    booking_date: $("input[name='booking_date']").val(),
	                    return_date: $("input[name='return_date']").val(),
	                    return_on: 0,
	                    dropoff_id: $('#dropoff_id').val(),
	                    fleet_id: $('#fleet_id').val(),
	                    voucher_code: $('#voucher_code').val(),
	                    payment_method: $('#payment_method').val()
	                };
	                if($("input[name='return_date']").length > 0 && $('#has_return').is(':checked'))
	                {
	                    opts.return_on = 1;
	                }*/
	                $.post("index.php?controller=pjAdminBookings&action=pjActionCalPrice", $form.serialize()).done(function (data) {
	                    $('#sub_total').val(parseFloat(data.sub_total).toFixed(2));
	                    $('#tax').val(parseFloat(data.tax).toFixed(2));
	                    $('#discount').val(parseFloat(data.discount).toFixed(2));
	                    $('#credit_card_fee').val(parseFloat(data.credit_card_fee).toFixed(2));
	                    $('#total').val(parseFloat(data.total).toFixed(2));
	                    $('#deposit').val(parseFloat(data.deposit).toFixed(2));                    
	                    $('#price').val(parseFloat(data.price).toFixed(2));
	                    $('#price_first_transfer').val(parseFloat(data.price_first_transfer).toFixed(2));
	                    $('#price_return_transfer').val(parseFloat(data.price_return_transfer).toFixed(2));
	                });
	            }
			}, 1000);
		}

        if (window.tinymce !== undefined) {
            tinymce.init({
                document_base_url: myLabel.install_url,
                relative_urls: false,
                remove_script_host: false,
                selector: "textarea.mceEditor",
                theme: "modern",
                width: 1000,
                height: 300,
                content_css: "app/web/css/emails.css",
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                    "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor"
                ],
                toolbar: "insertfile undo redo | styleselect fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
                setup: function(editor) {
                    editor.on('keydown', function(e) {
                        // Ignore Ctrl+S combination to prevent saving in TinyMCE as there is nothing to save.
                        if(e.ctrlKey && (e.which == 83)) {
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });
        }
        
        if ($('#frmSendCustomEmail').length > 0 && validate) {
        	$('#frmSendCustomEmail').validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
        
        if ($("#grid_history").length > 0 && datagrid) {
			var $grid_history = $("#grid_history").datagrid({
				buttons: [{type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteHistory&id={:id}"}],
				columns: [
				          {text: myLabel.h_content, type: "text", sortable: false, width:800},
				          {text: myLabel.h_by, type: "text", sortable: true, width:160},
				          {text: myLabel.h_created, type: "text", sortable: false, width:160}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetHistory" + pjGrid.queryString,
				dataType: "json",
				fields: ['action', 'created_by', 'created'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteHistoryBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: null,
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
        
        if ($("#grid_invoices").length > 0 && datagrid) {
        	function formatTotal(val, obj) {
    			return obj.total_formated;
    		}
        	
        	function formatDefault (str) {
    			return myLabel[str] || str;
    		}
    		
    		function formatId (str) {
    			return ['<a href="index.php?controller=pjInvoice&action=pjActionUpdate&id=', str, '">#', str, '</a>'].join("");
    		}
    		
    		function formatCreated(str) {
    			if (str === null || str.length === 0) {
    				return myLabel.empty_datetime;
    			}
    			
    			if (str === '0000-00-00 00:00:00') {
    				return myLabel.invalid_datetime;
    			}
    			
    			if (str.match(/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/) !== null) {
    				var x = str.split(" "),
    					date = x[0],
    					time = x[1],
    					dx = date.split("-"),
    					tx = time.split(":"),
    					y = dx[0],
    					m = parseInt(dx[1], 10) - 1,
    					d = dx[2],
    					hh = tx[0],
    					mm = tx[1],
    					ss = tx[2];
    				return $.datagrid.formatDate(new Date(y, m, d, hh, mm, ss), pjGrid.jsDateFormat + ", hh:mm:ss");
    			}
    		}
    		
    		var $grid_invoices = $("#grid_invoices").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjInvoice&action=pjActionUpdate&id={:id}", title: "Edit"},
				          {type: "delete", url: "index.php?controller=pjInvoice&action=pjActionDelete&id={:id}", title: "Delete"}],
				columns: [
				    {text: myLabel.num, type: "text", sortable: true, editable: false, renderer: formatId},
				    {text: myLabel.order_id, type: "text", sortable: true, editable: false},
				    {text: myLabel.issue_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.due_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.created, type: "text", sortable: true, editable: false, renderer: formatCreated},
				    {text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatDefault},	
				    {text: myLabel.total, type: "text", sortable: true, editable: false, align: "right", renderer: formatTotal}
				],
				dataUrl: "index.php?controller=pjInvoice&action=pjActionGetInvoices&q=" + $frmUpdateBooking.find("input[name='uuid']").val(),
				dataType: "json",
				fields: ['id', 'order_id', 'issue_date', 'due_date', 'created', 'status', 'total'],
				paginator: {
					actions: [
					   {text: myLabel.delete_title, url: "index.php?controller=pjInvoice&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_body}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				select: {
					field: "id",
					name: "record[]"
				}
			});
        }
        
        $(document).ready(function() {
            $('.collapse-header').on('click', function() {
                var $header = $(this);
                var targetId = $header.data('target');
                var $content = $(targetId);

                $content.slideToggle(300); 

                $header.toggleClass('active');
            });
            
            if (chosen) {
	            const statusColors = {
	                'pending': { code: '#F97316', class: 'is-pending' },      
	                'in_progress': { code: '#EF4444', class: 'is-progress' }, 
	                'confirmed': { code: '#10B981', class: 'is-confirmed' },  
	                'cancelled': { code: '#6B7280', class: 'is-cancelled' }   
	            };
	            $(".status-select").chosen({
	                width: "100%",
	                disable_search_threshold: 10 
	            });
	            $(".status-return-select").chosen({
	                width: "100%",
	                disable_search_threshold: 10 
	            });
	            var $statusContainer = $('.pj-status-color');
	            
	            $statusContainer.find('.status-select option').each(function(index) {
	                const option = $(this);
	                const value = option.val();
	                if (value && statusColors[value]) {
	                    const colorData = statusColors[value];
	                    $('#status_chzn_o_' + index).addClass(colorData.class);
	                    if ($(this).hasClass('.result-selected')) {
		                    const chosenSingle = $statusContainer.find('#status_chzn .chzn-single');
		                    chosenSingle.addClass(colorData.class);
	                    }
	                }
	            });
	            
	            $statusContainer.find('.status-select').on('change', function() {
	                const selectedValue = $(this).val();
	                const chosenSingleA = $statusContainer.find('#status_chzn .chzn-single');
	                if (selectedValue && statusColors[selectedValue]) {
	                    const colorData = statusColors[selectedValue];
	                    chosenSingleA.removeClass('is-pending')
	                    	.removeClass('is-progress')
	                    	.removeClass('is-confirmed')
	                    	.removeClass('is-cancelled');
	                    chosenSingleA.addClass(colorData.class);
	                }
	            }).trigger('change');
	            
	            // status return
	            var $statusReturnContainer = $('.pj-status-return-color');
	            $statusReturnContainer.find('.status-return-select option').each(function(index) {
	                const option = $(this);
	                const value = option.val();
	                if (value && statusColors[value]) {
	                    const colorData = statusColors[value];
	                    $('#status_return_trip_chzn_o_' + index).addClass(colorData.class);
	                    if ($(this).hasClass('.result-selected')) {
		                    const chosenSingle = $statusContainer.find('#status_return_trip_chzn .chzn-single');
		                    chosenSingle.addClass(colorData.class);
	                    }
	                }
	            });
	            
	            $statusReturnContainer.find('.status-return-select').on('change', function() {
	                const selectedValue = $(this).val();
	                const chosenSingleA = $statusReturnContainer.find('#status_return_trip_chzn .chzn-single');
	                if (selectedValue && statusColors[selectedValue]) {
	                    const colorData = statusColors[selectedValue];
	                    chosenSingleA.removeClass('is-pending')
	                	.removeClass('is-progress')
	                	.removeClass('is-confirmed')
	                	.removeClass('is-cancelled');
	                    chosenSingleA.addClass(colorData.class);
	                }
	            }).trigger('change');
            }
        });
        
        $(document).on("click", ".btnStartUpdateLatLng", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			startUpdateLatLng();
		});
        function startUpdateLatLng($page=1) {
			$('.pjUpdateLatLngWrapper').html('Please wait while updating....Do not refresh page.');
			$.get("index.php?controller=pjAdminBookings&action=pjActionProcessUpdateLatLng&page=" + $page, function(data) {
				if (data.next_page <= myLabel.totalPages) {
					startUpdateLatLng(data.next_page);
				} else {
					$('.pjUpdateLatLngWrapper').html('Bookings have been updated!');
				}
			});
		}
        
        if ($dialogSendWhatsappMessage.length > 0 && dialog) {
        	$dialogSendWhatsappMessage.on("change", "select[name='locale_id']", function (e) {
    			if (e && e.preventDefault) {
    				e.preventDefault();
    			}
    			var $id = $('#frmSendWs').find('input[name="id"]').val(),
	    			$booking_id = $('#frmSendWs').find('input[name="booking_id"]').val(),
	    			$locale_id = $(this).val(),
	    			$href = 'index.php?controller=pjAdminBookings&action=pjActionWhatsapp&id='+$id+'&booking_id='+$booking_id+'&locale_id=' + $locale_id;
    			$.get($href, function(data) {
    				$('#dialogSendWhatsappMessageContent').html(data);
    			});
    			
    			return false;
    		})
        	$dialogSendWhatsappMessage.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				width: 500,
				buttons: (function () {
					var buttons = {};
					buttons[myLabel.btnSend] = function () {
						var $form = $dialogSendWhatsappMessage.find("form"),
						$c_phone = $form.find('input[name="customer_phone"]').val(),
						$text = $form.find('textarea[name="message"]').val();
						$dialogSendWhatsappMessage.dialog("close");
						var $href = 'https://wa.me/'+$c_phone+'?text='+$text;
						window.open($href, '_blank');
					};
					buttons[myLabel.btnCancel] = function () {
						$dialogSendWhatsappMessage.dialog("close");
					};
					
					return buttons;
				})()
			});
		}

	});
})(jQuery_1_8_2);