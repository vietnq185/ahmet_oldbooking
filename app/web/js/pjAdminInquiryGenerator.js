var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmInquiryGenerator = $("#frmInquiryGenerator"),
			$frmSendInquiry = $("#frmSendInquiry"),
			validate = ($.fn.validate !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			spinner = ($.fn.spinner !== undefined),
			chosen = ($.fn.chosen !== undefined);
		
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
			$(".select-item").chosen();
		}
		
		if ($frmSendInquiry.length > 0 && validate) {
			$frmSendInquiry.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmInquiryGenerator.length > 0) 
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
			
			$frmInquiryGenerator.validate({
				rules: {
					passengers: {
						positiveNumber: true,
						maximumNumber: true
					},
					passengers_return: {
						positiveNumber: true,
						maximumNumber: true
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'booking_date' || element.attr('name') == 'passengers' || element.attr('name') == 'passengers_return')
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
				submitHandler: function (form) {
					$('.bs-loader').show();
					$('.inquiryTemplate').html('');
					$.post('index.php?controller=pjAdminInquiryGenerator&action=pjActionGenerateInquiry', $(form).serialize()).done(function (data) {
						$('.inquiryTemplate').html(data);	
						if ($('.mceEditor').length > 0) {
							myTinyMceDestroy.call(null);
							myTinyMceInit.call(null, 'textarea.mceEditor');
				        }
						$('.bs-loader').hide();
	        		});
					return false;
				}
			});
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
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
							if ($frmInquiryGenerator.length) {
								calPrice($frmInquiryGenerator);
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
		}).on("change", "#location_id", function (e) {
			$.get("index.php?controller=pjAdminInquiryGenerator&action=pjActionGetDropoff", {
				location_id: $(this).val()
			}).done(function (data) {
				$("#trDropoffContainer").html(data);
				$("#dropoff_id").chosen();
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
		}).on("change", "#fleet_id", function (e) {
			var $form = $(this).closest('form');
			calPrice($form);

			var passengers = parseInt($('#fleet_id').find(':selected').attr('data-passengers'), 10),
				curr_passengers = parseInt($('#passengers').val(),10),
				curr_passengers_return = parseInt($('#passengers_return').val(),10);
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
		});
		
		function calPrice($form)
		{
			setTimeout(function() {
	            if($('#dropoff_id').val() != '' && $('#fleet_id').val() != '' && $("input[name='booking_date']").val() != '')
	            {
	                $.post("index.php?controller=pjAdminInquiryGenerator&action=pjActionCalPrice", $form.serialize()).done(function (data) {
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
		
		function myTinyMceDestroy() {
			
			if (window.tinymce === undefined) {
				return;
			}
			
			var iCnt = tinymce.editors.length;
			
			if (!iCnt) {
				return;
			}
			
			for (var i = 0; i < iCnt; i++) {
				tinymce.remove(tinymce.editors[i]);
			}
		}
		
		function myTinyMceInit(pSelector) {
			
			if (window.tinymce === undefined) {
				return;
			}
			
			tinymce.init({
                document_base_url: myLabel.install_url,
                relative_urls: false,
                remove_script_host: false,
                selector: "textarea.mceEditor",
                theme: "modern",
                width: 1000,
                height: 300,
                //content_css: "app/web/css/emails.css",
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