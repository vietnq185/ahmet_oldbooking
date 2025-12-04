<?php
$STORE = @$tpl['store'];
$FORM = @$tpl['form']['departure'];
$index = pjObject::escapeString($_GET['index']);
?>
<div class="row">
    <!--- Content -->
    <div class="full-width content">
        <header class="f-title color"><?= str_replace('{X}', 4, __('front_step_x', true, false)) ?><?php __('front_step_departure_info'); ?></header>
    </div>
    <!--- //Content -->

    <div class="three-fourth">
        <?php if($tpl['status'] == 'OK'): ?>
            <form id="trDepartureForm_<?php echo $index;?>" action="" method="post">
                <input type="hidden" name="index" value="<?= $index ?>">
                <input type="hidden" name="original_date" id="trDateOriginal_<?php echo $index?>" value="<?php echo isset($STORE['search']) && isset($STORE['search']['date']) ? htmlspecialchars($STORE['search']['date']) : null; ?>">
				
        		<div class="f-row">


<?php if ($STORE['search']['is_airport']) { ?>
					<div class="f-row">
					<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-alert-success" aria-hidden="true"><div class="vc_message_box-icon"><i class="fas fa-plane-arrival"></i></div><p></i> <?php __('front_stress_free');?></p></div>
					</div>
				<?php } ?>
				
					<div><?php __('front_your_transfer_from');?>: <span class="bold"><?php echo pjSanitize::clean($tpl['cart']['pickup_location_name']);?></span></div>
				</div>
				
                <div class="f-row">
                    <div class="form-group datepicker one-half">
                        <label for="trDateConfirm_<?php echo $index?>"><?php __('front_verify_departure_date'); ?></label>
                        <div>
                            <input type="text" placeholder="<?php __('front_select_transfer_date', false, true); ?>" id="trDateConfirm_<?php echo $index?>" name="date_confirm" readonly value="<?php echo isset($STORE['search']) && isset($STORE['search']['date']) ? htmlspecialchars($STORE['search']['date']) : null; ?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                        </div>
                    </div>
                    <div class="one-half">
                        <label for="passengers"><?php __('front_number_of_passengers'); ?></label>
                        <select name="passengers" id="passengers" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                            <option value=""><?php __('front_choose', false, false); ?></option>
                            <?php for($i = $tpl['fleet']['min_passengers']; $i <= $tpl['fleet']['passengers']; $i++): ?>
                                <option value="<?= $i ?>"<?= $FORM['passengers'] == $i? ' selected="selected"': null; ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="f-row" id="trDateConfirmMsg_<?php echo $index?>" style="display: none;"><?php __('front_date_change_message') ?></div>
			
				<div class="f-row"><div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-info"><div class="vc_message_box-icon"><i class="fa fa-info-circle"></i></div><p>
						<?php
						if ((int)$tpl['dropoff_arr']['is_airport'] == 0) { 
							echo sprintf(__('front_recommended_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], $tpl['dropoff_arr']['duration']);
						} else {
							echo sprintf(__('front_recommended_airport_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], $tpl['dropoff_arr']['duration']);
						} ?></p></div>
				</div>
				<?php if ($STORE['search']['is_airport']) { ?>
					<div class="f-row">
					
					</div>
					<div class="f-row"><?php __('front_select_flight_landing_time');?>:</div>
				<?php } ?>
				<?php if(!$STORE['search']['is_airport'] && (int)$tpl['dropoff_arr']['is_airport'] == 0) { ?>
					<div class="f-row">
                        <div class="one-fourth micro time_h">
                            <label for="time_h"><?php __('front_pickup_time'); ?> (<?php __('front_hour'); ?>)</label>
                            <select name="time_h" id="time_h" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                                <option value="">Hh</option>
                                <?php for($i = 0; $i <= 23; $i++): ?>
                                    <option value="<?= $i ?>"<?= isset($FORM['time_h']) && $FORM['time_h'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                            
                        </div>

                        <div class="one-fourth micro time_m">
                            <label for="time_m"><?php __('front_pickup_time'); ?> (<?php __('front_minute'); ?>)</label>
                            <select name="time_m" id="time_m" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                                <option value="">Mm</option>
                                <?php for($i = 0; $i <= 55; $i += 5): ?>
                                    <option value="<?= $i ?>"<?= isset($FORM['time_m']) && $FORM['time_m'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="one-half">
                            <label for="c_address"><?php __('front_pickup_address'); ?></label>
                            <input type="text" name="c_address" id="c_address" value="<?php echo isset($FORM['c_address']) ? pjSanitize::clean($FORM['c_address']) : null;?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                        </div>
                    </div>
                    
                    <div class="f-row">
						<div><?php __('front_going_to');?>: <span class="bold"><?php echo pjSanitize::clean($tpl['cart']['dropoff_location_name']);?></span></div>
					</div>
					
					<div class="f-row">
						<div class="one-half">
							<label for="c_destination_address"><?php __('front_dropoff_address'); ?></label>
                            <input type="text" name="c_destination_address" id="c_destination_address" value="<?php echo isset($FORM['c_destination_address']) ? pjSanitize::clean($FORM['c_destination_address']) : null;?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						</div></div>
						<div class="f-row">
                        <div class="one-half">
                            <label for="c_notes"><?php __('front_notes'); ?></label>

                            <textarea name="c_notes" id="c_notes"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
                        </div>
                    </div>
				<?php } else { ?>
	                <?php if($STORE['search']['is_airport']): ?>
	                    <div class="f-row">
	                        <div class="one-fourth micro time_h">
	                            <label for="time_h"><?php __('front_flight_time'); ?> (<?php __('front_hour'); ?>)</label>
	                            <select name="time_h" id="time_h" class="required">
	                                <option value="">Hh</option>
	                                <?php for($i = 0; $i <= 23; $i++): ?>
	                                    <option value="<?= $i ?>"<?= isset($FORM['time_h']) && $FORM['time_h'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
	                                <?php endfor; ?>
	                            </select>
	                            
	                        </div>
	                        <div class="one-fourth micro time_m">
	                            <label for="time_m"><?php __('front_flight_time'); ?> (<?php __('front_minute'); ?>)</label>
	                            <select name="time_m" id="time_m" class="required">
	                                <option value="">Mm</option>
	                                <?php for($i = 0; $i <= 55; $i += 5): ?>
	                                    <option value="<?= $i ?>"<?= isset($FORM['time_m']) && $FORM['time_m'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
	                                <?php endfor; ?>
	                            </select>
	                        </div>
	
	                        <div class="one-fourth">
	                            <label for="c_flight_number"><?php __('front_flight_number'); ?></label>
	                            <input type="text" name="c_flight_number" id="c_flight_number" value="<?php echo isset($FORM['c_flight_number']) ? pjSanitize::clean($FORM['c_flight_number']) : null;?>"/>
	                        </div>
	
	                        <div class="one-fourth">
	                            <label for="c_airline_company"><?php __('front_airline_company'); ?></label>
	                            <input type="text" name="c_airline_company" id="c_airline_company" value="<?php echo isset($FORM['c_airline_company']) ? pjSanitize::clean($FORM['c_airline_company']) : null;?>"/>
	                        </div>
	                    </div>
						<?php if ($STORE['search']['is_airport']) { ?>
							<div class="f-row">
								<div><?php __('front_going_to');?>: <span class="bold"><?php echo pjSanitize::clean($tpl['cart']['dropoff_location_name']);?></span></div>
							</div>
						<?php } ?>
	                    <div class="f-row">
	                        <div class="one-half">
	                            <label for="c_destination_address"><?php __('front_destination_address'); ?></label>
	                            <input type="text" name="c_destination_address" id="c_destination_address" value="<?php echo isset($FORM['c_destination_address']) ? pjSanitize::clean($FORM['c_destination_address']) : null;?>"/>
	                           </div>
	                           <div class="one-half">
	                            <label for="c_hotel"><?php __('front_hotel'); ?></label>
	                            <input type="text" name="c_hotel" id="c_hotel" value="<?php echo isset($FORM['c_hotel']) ? pjSanitize::clean($FORM['c_hotel']) : null;?>"/>
	                        </div>
	                        
	                        </div>
	                        <div class="f-row">
	                        <div class="one-half">
	                            <label for="c_notes"><?php __('front_notes'); ?></label>
	
	                            <textarea name="c_notes" id="c_notes"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
	                        </div>
	                    </div>
	                <?php else: ?>
	                    <div class="f-row">
	                        <div class="one-fourth micro time_h">
	                            <label for="time_h"><?php __('front_pickup_time'); ?> (<?php __('front_hour'); ?>)</label>
	                            <select name="time_h" id="time_h" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
	                                <option value="">Hh</option>
	                                <?php for($i = 0; $i <= 23; $i++): ?>
	                                    <option value="<?= $i ?>"<?= isset($FORM['time_h']) && $FORM['time_h'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
	                                <?php endfor; ?>
	                            </select>
	                            
	                        </div>
	
	                        <div class="one-fourth micro time_m">
	                            <label for="time_m"><?php __('front_pickup_time'); ?> (<?php __('front_minute'); ?>)</label>
	                            <select name="time_m" id="time_m" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
	                                <option value="">Mm</option>
	                                <?php for($i = 0; $i <= 55; $i += 5): ?>
	                                    <option value="<?= $i ?>"<?= isset($FORM['time_m']) && $FORM['time_m'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
	                                <?php endfor; ?>
	                            </select>
	                        </div>
						</div>
						<div class="f-row">	                            
	                             <div class="one-half">
		                            <label for="c_address"><?php __('front_address'); ?></label>
		                            <input type="text" name="c_address" id="c_address" value="<?php echo isset($FORM['c_address']) ? pjSanitize::clean($FORM['c_address']) : null;?>"/>
		                        </div>	                    
	                        	<div class="one-half">
		                            <label for="c_hotel"><?php __('front_hotel'); ?></label>
		                            <input type="text" name="c_hotel" id="c_hotel" value="<?php echo isset($FORM['c_hotel']) ? pjSanitize::clean($FORM['c_hotel']) : null;?>"/>
	                            </div>
	                        </div>
	                        <div class="f-row">
								<div><?php __('front_going_to');?>: <span class="bold"><?php echo pjSanitize::clean($tpl['cart']['dropoff_location_name']);?></span></div>
							</div>
	                       <div class="f-row">
	                            <div class="one-fourth micro time_h">
	                                <label for="c_departure_flight_time_h"><?php __('front_flight_departure_time'); ?> (<?php __('front_hour'); ?>)</label>
	                                <select name="c_departure_flight_time_h" id="c_flight_time_h">
	                                    <option value="">Hh</option>
	                                    <?php for($i = 0; $i <= 23; $i++): ?>
	                                        <option value="<?= $i ?>"<?= isset($FORM['c_departure_flight_time_h']) && $FORM['c_departure_flight_time_h'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
	                                    <?php endfor; ?>
	                                </select>
	                               
	                            </div>
	
	                            <div class="one-fourth micro time_m">
	                                <label for="c_departure_flight_time_m"><?php __('front_flight_departure_time'); ?> (<?php __('front_minute'); ?>)</label>
	                                <select name="c_departure_flight_time_m" id="c_departure_flight_time_m">
	                                    <option value="">Mm</option>
	                                    <?php for($i = 0; $i <= 55; $i += 5): ?>
	                                        <option value="<?= $i ?>"<?= isset($FORM['c_departure_flight_time_m']) && $FORM['c_departure_flight_time_m'] == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
	                                    <?php endfor; ?>
	                                </select>
	                            </div>
							</div>
	                           
                            <div class="f-row">	                        
		                        <div class="one-half">
		                            <label for="c_notes"><?php __('front_notes'); ?></label>
		
		                            <textarea name="c_notes" id="c_notes"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
		                        </div>
		                    </div>
	                <?php endif; ?>
				<?php } ?>
                <div class="trCheckErrorMsg" align="right"></div>
                <div class="actions">
                    <button type="submit" class="btn medium color right"><?php __('front_btn_continue'); ?></button>
                </div>
                
            
            </form>
        <?php else: ?>
            <div class="trSystemMessage"><?php __('front_error'); ?></div>
        <?php endif; ?>
            
    </div>

    <?php include_once PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?>
</div>

                
                </div>
