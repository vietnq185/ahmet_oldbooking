var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var 
			$frmCreateBooking = $('#frmCreateBooking'),
			$frmUpdateBooking = $('#frmUpdateBooking'),
			$dialogSelect = $("#dialogSelect"),
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
			};
	
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
				          {text: myLabel.transfer_date_time, type: "text", sortable: false, width:130},
				          {text: myLabel.transfer_destinations, type: "text", sortable: false, width:160},
				          {text: myLabel.fleet, type: "text", sortable: true, width:220},
				          {text: myLabel.passengers, type: "text", sortable: true, width:100, align: "center"},
				          {text: myLabel.extras, type: "text", sortable: false, width:160},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 110, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.passed_on, value: "passed_on"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['client', 'date_time', 'pickup_dropoff', 'fleet', 'passengers', 'extras', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", render: false, ajax: false},
					   {text: myLabel.print, url: "javascript:void(0);", render: false},
					   {text: myLabel.print_reservation_details, url: "javascript:void(0);", render: false}
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
                        calPrice();
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
                            calPrice();
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
				email: ""
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
				email: ""
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
				email: ""
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
            calPrice();
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
            calPrice();

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
            calPrice();

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
			}else{
				$("input[name='return_date']").val("");
				$("#return_date_outer").hide(); 
				$('.trReturnDetails').hide();
			}

            calPrice();
		}).on("change", "#voucher_code", function (e) {
            calPrice();
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
        });
		
		$("#grid").on("click", 'ul.pj-menu-list li:nth-child(3) a', function (e) {console.log(33)
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				window.open('index.php?controller=pjAdminBookings&action=pjActionPrint&record=' + booking_id,'_blank');
			}	
			return false;
		}).on("click", 'ul.pj-menu-list li:nth-child(4) a', function (e) {console.log(33)
			e.preventDefault();
			var booking_id = $('.pj-table-select-row:checked').map(function(e){
				 return $(this).val();
			}).get();
			if(booking_id != '' && booking_id != null)
			{
				window.open('index.php?controller=pjAdminBookings&action=pjActionPrint&details&record=' + booking_id,'_blank');
			}	
			return false;
		});
		
		function calPrice()
		{
            if($('#dropoff_id').val() != '' && $('#fleet_id').val() != '' && $("input[name='booking_date']").val() != '')
            {
                var opts = {
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
                }
                $.get("index.php?controller=pjAdminBookings&action=pjActionCalPrice", opts).done(function (data) {
                    $('#sub_total').val(parseFloat(data.sub_total).toFixed(2));
                    $('#tax').val(parseFloat(data.tax).toFixed(2));
                    $('#discount').val(parseFloat(data.discount).toFixed(2));
                    $('#total').val(parseFloat(data.total).toFixed(2));
                    $('#deposit').val(parseFloat(data.deposit).toFixed(2));
                });
            }
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
	});
})(jQuery_1_8_2);