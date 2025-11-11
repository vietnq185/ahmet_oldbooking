var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateFleet = $("#frmCreateFleet"),
			$frmUpdateFleet = $("#frmUpdateFleet"),
			$dialogDelete = $("#dialogDeleteImage"),
			dialog = ($.fn.dialog !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			spinner = ($.fn.spinner !== undefined),
			$datepick = $(".datepick"),
			dOpts = {};
		
		if ($datepick.length > 0) {
			dOpts = $.extend(dOpts, {
				firstDay: $datepick.attr("rel"),
				dateFormat: $datepick.attr("rev"),
				changeMonth: true,
				changeYear: true
			});
		}
		
		$(".field-int").spinner({
			min: 0
		});

		if (window.tinymce !== undefined) {
			tinymce.init({
				document_base_url: myLabel.install_url,
				relative_urls: false,
				remove_script_host: false,
				selector: "textarea.mceEditor",
				theme: "modern",
				width: 750,
				height: 300,
				content_css: "app/web/css/emails.css",
				plugins: [
					"advlist autolink link image lists charmap print preview hr anchor pagebreak",
					"searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
					"save table contextmenu directionality emoticons template paste textcolor"
				],
				toolbar: "insertfile undo redo | styleselect fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
			});
		}
		
		if ($frmCreateFleet.length > 0) {
			$frmCreateFleet.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($frmUpdateFleet.length > 0) {
			$frmUpdateFleet.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($dialogDelete.length > 0 && dialog) 
		{
			$dialogDelete.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[trApp.locale.button.yes] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDelete.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#image_container').remove();
									$dialogDelete.dialog('close');
								}
							}
						});
					};
					buttons[trApp.locale.button.no] = function () {
						$dialogDelete.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		function formatImage(val, obj) {
			var src = val != null ? val : 'app/web/img/backend/no-image.png';
			return ['<a href="index.php?controller=pjAdminFleets&action=pjActionUpdate&id=', obj.id ,'"><img src="', src, '" style="width: 100px" /></a>'].join("");
		}
		if ($("#grid").length > 0 && datagrid) {
			
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminFleets&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminFleets&action=pjActionDeleteFleet&id={:id}"}
				          ],
				columns: [{text: myLabel.thumb, type: "text", sortable: false, editable: false, renderer: formatImage, width: 110},
				          {text: myLabel.fleet, type: "text", sortable: true, width: 700, editable: true, editableWidth: 600},
				          {text: myLabel.passengers, type: "text", sortable: true, width: 100, editable: false, editableWidth: 70},
				          {text: myLabel.luggage, type: "text", sortable: true, width: 80, editable: true, editableWidth: 70},
				          {text: myLabel.status, type: "select", sortable: true, width: 100, editable: true, editableWidth: 90,options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminFleets&action=pjActionGetFleet",
				dataType: "json",
				fields: ['thumb_path', 'fleet', 'passengers', 'luggage', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminFleets&action=pjActionDeleteFleetBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminFleets&action=pjActionStatusFleet", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminFleets&action=pjActionExportFleet", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminFleets&action=pjActionSaveFleet&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminFleets&action=pjActionGetFleet", "fleet", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminFleets&action=pjActionGetFleet", "fleet", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminFleets&action=pjActionGetFleet", "fleet", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDelete.data('href', $(this).data('href')).dialog("open");
		}).on("change", ".pjAdditionalDiscountValid", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $parent = $(this).closest('p');
			if ($(this).val() == 'always') {
				$parent.find('.pjMainDiscountPeriod').show();
				$parent.find('.pjAdditionalDiscountPeriod').hide();
			} else {
				$parent.find('.pjMainDiscountPeriod').hide();
				$parent.find('.pjAdditionalDiscountPeriod').show();
			}
		}).on("focusin", ".datepick", function (e) {
			$(this).datepicker(dOpts);
		}).on('click', '[data-period]', function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}

			var action = $(this).data('period'),
				$level = $(this).data('level');
			if (action == 'add') {
				var index = 'new_' + Math.round(Math.random() * 10000);
				var weekday = $(this).data('weekday');
				var datepick = 'datepick';
				var row = $('[data-period="copy"]').html();

				row = row.replace(/\{LEVEL\}/g, $level);
				row = row.replace(/\{INDEX\}/g, index);
				row = row.replace(/\{WEEKDAY\}/g, weekday);
				row = row.replace(/\{DATEPICK\}/g, datepick);
				$(this).closest('[data-period="paste"]').append(row);
			}
			else if (action == 'remove') {
				$(this).closest('.period-dates').remove();
			}
		});
	});
})(jQuery_1_8_2);