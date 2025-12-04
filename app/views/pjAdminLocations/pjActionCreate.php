<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
    include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	pjUtil::printNotice(__('infoAddLocationTitle', true, false), __('infoAddLocationDesc', true, false));
	
	$index = 'tr_' . rand(1, 999999);
    $yesno = __('_yesno', true);
    $_price_levels = __('_price_levels', true);
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionCreate" method="post" id="frmCreateLocation" class="pj-form form">
		<input type="hidden" name="location_create" value="1" />
		<input type="hidden" id="index_arr" name="index_arr" value="<?php echo $index;?>" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif;?>
		<div class="clear_both">
			<div class="p">
				<label class="title"><?php __('lblPickupLocation'); ?>:</label>
				<div class="overflow">
					<table>
						<tr>
							<td>
								<select name="location_icon" class="pj-form-field required" style="margin-top: -10px;">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<option value="airport"><?php __('lblIconAirport'); ?></option>
									<option value="train"><?php __('lblIconTrain'); ?></option>
									<option value="city"><?php __('lblIconCity'); ?></option>
									<option value="skiing"><?php __('lblIconSkiing'); ?></option>
								</select>
							</td>
							<td>
								<?php
									foreach ($tpl['lp_arr'] as $v)
									{
										?>
										<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
											<span class="inline_block">
												<input type="text" name="i18n[<?php echo $v['id']; ?>][pickup_location]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" />
												<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
													<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
												<?php endif;?>
											</span>
										</p>
										<?php
									}
								?>
							</td>
							<td>
								<p><input type="text" name="location_region" placeholder="<?php __('lblTransferRegion');?>" class="pj-form-field w300" /></p>
							</td>
						</tr>
					</table>
				</div>
			</div>
			
			<p>
				<label class="title"><?php __('lblPickupLocation'); ?></label>
				<span class="inline_block">
					<input type="text" class="pj-form-field w400 required" name="address" value="<?php echo $tpl['arr']['address']; ?>">
				</span>
			</p>
			
            <p>
                <label class="title"><?php __('lblIsAirport'); ?></label>
                <span class="inline_block">
                    <select name="is_airport" id="is_airport" class="pj-form-field required">
                        <option value="">-- <?php __('lblChoose'); ?>--</option>
                        <option value="0"><?php echo $yesno['F']; ?></option>
                        <option value="1"><?php echo $yesno['T']; ?></option>
                    </select>
                </span>
            </p>
			<p data-order-index="1" style="display: none;">
				<label class="title"><?php __('lblOrderIndex'); ?></label>
				<span class="inline_block">
					<input type="text" class="pj-form-field w60 field-int" name="order_index" >
				</span>
			</p>
			
			<div>
    			<label class="title"><?php __('lblColor'); ?></label>
    			<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="color" class="pj-form-field colorSelector w60" />
					<span class="pj-form-field-after"></span>
				</span>
    		</div>
    		<div class="clear_both"></div><br/>
    		
			<div id="tr_dropoff_container" class="p tr-dropoff-container">
				<label class="title"><?php __('lblDropoff'); ?>:</label>
				<div class="overflow">
					<table id="tr_dropoff_table" class="tr-dropoff-table">
						<tr>
							<td>
    							<label class="tr-column-name">&nbsp;</label>
    						</td>
							<td>
								<label class="tr-column-name"><?php __('lblIcon'); ?></label>
							</td>
							<td>
								<label class="tr-column-name"><?php __('lblLocation');?></label>
								<?php 
								foreach ($tpl['lp_arr'] as $v)
								{ 
									?>
									<p class="pj-column-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
										<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
										<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
										<?php endif;?>
									</p>
									<?php
								} 
								?>
							</td>
							<td><?php __('lblAddress'); ?></td>
							<td><?php __('lblDuration'); ?>&nbsp;(<label class="lowercase"><?php __('lblMinutes'); ?></label>)</td>
							<td><?php __('lblDistance'); ?>&nbsp;(<label class="lowercase"><?php __('lblKm'); ?></label>)</td>
							<td><?php __('lblAirport'); ?></td>
							<td><?php __('lblOrderIndex'); ?></td>
							<td><?php __('lblTransferRegion'); ?></td>
							<td>&nbsp;</td>
						</tr>
						<tr class="tr-location-row" data-index="<?php echo $index;?>">
							<td>
								<div class="p">
									<select name="price_level[<?php echo $index; ?>]" class="pj-form-field required">
										<?php foreach ($_price_levels as $pk => $pv) { ?>
											<option value="<?php echo $pk;?>" <?php echo $pk == 1 ? 'selected="selected"' : '';?>><?php echo $pv; ?></option>
										<?php } ?>
									</select>
								</div>
							</td>
							<td>
								<div class="p">
									<select name="icon[<?php echo $index; ?>]" class="pj-form-field required">
										<option value="">-- <?php __('lblChoose'); ?> --</option>
										<option value="airport"><?php __('lblIconAirport'); ?></option>
										<option value="train"><?php __('lblIconTrain'); ?></option>
										<option value="city"><?php __('lblIconCity'); ?></option>
										<option value="skiing"><?php __('lblIconSkiing'); ?></option>
									</select>
								</div>
							</td>
							<td>
								<?php
								foreach ($tpl['lp_arr'] as $v)
								{
									?>
									<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
										<span class="inline_block">
											<input type="text" name="i18n[<?php echo $v['id']; ?>][location][<?php echo $index;?>]"  class="pj-form-field w110" lang="<?php echo $v['id']; ?>" />
										</span>
									</p>
									<?php
								} 
								?>
							</td>
							<td>
								<p>
									<span class="inline_block">
										<input type="text" name="dropoff_address[<?php echo $index;?>]"  class="pj-form-field w120" />
									</span>
								</p>
							</td>
							<td>
								<p>
									<span class="inline_block">
										<input type="text" id="duration_<?php echo $index;?>" name="duration[<?php echo $index;?>]" class="pj-form-field field-int w60 pj-positive-number"/>
									</span>
								</p>
								
							</td>
							<td>
								<p>
									<span class="inline_block">
										<input type="text" id="distance_<?php echo $index;?>" name="distance[<?php echo $index;?>]" class="pj-form-field field-int w60 pj-positive-number"/>
									</span>
								</p>
							</td>
							<td>
								<p>
									<span class="inline_block">
										<select name="airport[<?php echo $index; ?>]" id="airport_<?php echo $index; ?>" class="pj-form-field w80">
					                        <option value="0" selected="selected"'><?php echo $yesno['F']; ?></option>
					                        <option value="1"><?php echo $yesno['T']; ?></option>
					                    </select>
									</span>
								</p>
							</td>
							<td>
								<p>
									<span class="inline_block">
										<input type="text" class="pj-form-field w60 field-int" disabled name="order_index[<?php echo $index; ?>]" id="order_index_<?php echo $index; ?>" value="">
									</span>
								</p>
							</td>
							<td>
								<p>
									<span class="inline_block">
										<input type="text" id="region_<?php echo $index; ?>" name="region[<?php echo $index; ?>]" class="pj-form-field w100" />
									</span>
								</p>
							</td>
							<td>
								<p>
									<span class="inline_block">
										<a href="#" class="pj-remove-dropoff"></a>
									</span>
								</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button pj-add-dropoff" />
				<?php
				if(!empty($tpl['location_arr']))
				{ 
					?>
					<span class="inline_block r20"><a href="#" class="tr_copy_location" data-type="location"><?php __('lblCopyLocation');?></a></span>
					<span class="inline_block"><a href="#" class="tr_copy_location" data-type="location_price"><?php __('lblCopyLocationAndPrice');?></a></span>
					<?php
				} 
				?>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminLocations&action=pjActionIndex';" />
			</p>
		</div>
	</form>
	
	<table id="tr_dropoff_table_clone" style="display: none;">
		<tr class="tr-location-row" data-index="{INDEX}">
			<td>
				<div class="p">
					<select name="price_level[{INDEX}]" class="pj-form-field required">
						<?php foreach ($_price_levels as $pk => $pv) { ?>
							<option value="<?php echo $pk;?>" <?php echo $pk == 1 ? 'selected="selected"' : '';?>><?php echo $pv; ?></option>
						<?php } ?>
					</select>
				</div>
			</td>
			<td>
				<div class="p">
					<select name="icon[{INDEX}]" class="pj-form-field required">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<option value="airport"><?php __('lblIconAirport'); ?></option>
						<option value="train"><?php __('lblIconTrain'); ?></option>
						<option value="city"><?php __('lblIconCity'); ?></option>
						<option value="skiing"><?php __('lblIconSkiing'); ?></option>
					</select>
				</div>
			</td>
			<td>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][location][{INDEX}]"  class="pj-form-field w110" lang="<?php echo $v['id']; ?>" />
						</span>
					</p>
					<?php
				} 
				?>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" name="dropoff_address[{INDEX}]"  class="pj-form-field w120" />
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" id="duration_{INDEX}" name="duration[{INDEX}]" class="pj-form-field {SPINNER} w60 pj-positive-number"/>
					</span>
				</p>
				
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" id="distance_{INDEX}" name="distance[{INDEX}]" class="pj-form-field {SPINNER} w60 pj-positive-number"/>
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<select name="airport[{INDEX}]" id="airport_{INDEX}" class="pj-form-field w80">
	                        <option value="0" selected="selected"'><?php echo $yesno['F']; ?></option>
	                        <option value="1"><?php echo $yesno['T']; ?></option>
	                    </select>
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" class="pj-form-field w60 {SPINNER}" disabled name="order_index[{INDEX}]" id="order_index_{INDEX}" value="">
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" id="region_{INDEX}" name="region[{INDEX}]" class="pj-form-field w100" />
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<a href="#" class="pj-remove-dropoff"></a>
					</span>
				</p>
			</td>
		</tr>
	</table>
	
	<div id="dialogCopy"  class="trCopyDialog pj-form form" style="display: none" title="<?php __('lblCopyDropoff');?>">
		<p>
			<label class="title"><?php __('lblLocation'); ?>:</label>
			<span class="inline-block">
				<select name="location_id" id="location_id" class="pj-form-field w200 required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach($tpl['location_arr'] as $k => $v)
					{
						?><option value="<?php echo $v['id'];?>"><?php echo $v['pickup_location'];?></option><?php
					} 
					?>
				</select>
			</span>
		</p>
	</div>
	
	<div id="dialogLocationsStatus" title="<?php echo pjSanitize::html(__('lblStatusTitle', true)); ?>" style="display: none">
		<span class="bxLocationStatus bxLocationStatusStart" style="display: none"><?php __('lblStatusStart'); ?></span>
		<span class="bxLocationStatus bxLocationStatusEnd" style="display: none"><?php __('lblStatusEnd'); ?></span>
		<span class="bxLocationStatus bxLocationStatusFail" style="display: none"><?php __('lblStatusFail'); ?></span>
	</div>
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var fleet_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('tr_field_required'); ?>";
	myLabel.positive_number = "<?php __('lblPositiveNumber'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	foreach ($tpl['fleet_arr'] as $v)
	{
		?>fleet_array.push(<?php echo $v['id'];?>);<?php
	}
	?>
	myLabel.locale_array = locale_array;
	myLabel.fleet_array = fleet_array;
	myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					$('.pj-column-multilang-wrap').css('display','none');
					$(".pj-column-multilang-wrap[data-index='" + ui.index + "']").css('display','block');
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>