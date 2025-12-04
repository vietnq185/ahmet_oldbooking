<main class="main booking" role="main">
   
    <div class="wrap">
    	<div class="row">
        	<div class="full-width sum"><br/><br/>
        		<?php if ((int)$tpl['arr']['paid_via_payment_link'] == 1) { ?>
        			<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-alert-success"><div class="vc_message_box-icon"><i class="fas fa-check-circle"></i></div><h2 id="booking_gtm"><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_2', true)); ?></h2>
		           	<br/>
					<p><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_2_desc', true)); ?></p></div>
	        	<?php } elseif ($hours < 24 || $tpl['arrivalNotice'] > 0) { ?>
					<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-alert-success"><div class="vc_message_box-icon"><i class="fas fa-check-circle"></i></div><h2 id="booking_gtm"><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_1', true)); ?></h2>
		           	<br/>
		            <p><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_1_desc', true)); ?></p></div>
		        <?php } else { ?>
					<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-alert-success"><div class="vc_message_box-icon"><i class="fas fa-check-circle"></i></div><h2 id="booking_gtm"><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary', true)); ?></h2>
		           	<br/>
					<p><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_desc', true)); ?></p></div>
		        <?php } ?>
			</div>		   
        </div>
        <div class="row">
            <div class="full-width">
                <form class="box readonly">
                    <h3><?php __('front_step_passenger_details'); ?></h3>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_name_surname'); ?></div>
                        <div class="three-fourth"><?= __('personal_titles_ARRAY_' . $tpl['arr']['c_title'], true, false) . ' ' .  $tpl['arr']['c_fname'] . ' ' . $tpl['arr']['c_lname'] ?></div>
                    </div>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_mobile_number'); ?></div>
                        <div class="three-fourth"><?= $tpl['arr']['c_dialing_code'] . $tpl['arr']['c_phone'] ?></div>
                    </div>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_email'); ?></div>
                        <div class="three-fourth"><?= $tpl['arr']['c_email'] ?></div>
                    </div>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_country'); ?></div>
                        <div class="three-fourth"><?= $tpl['country_arr']['country_title'] ?></div>
                    </div>

                    <h3><?php __('front_transfer_details'); ?></h3>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_date'); ?></div>
                        <div class="three-fourth"><?= date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['booking_date'])) ?></div>
                    </div>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_time'); ?></div>
                        <div class="three-fourth"><?= date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date'])) ?></div>
                    </div>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_cart_from'); ?></div>
                        <div class="three-fourth"><?= $tpl['pickup_arr']['pickup_location'] ?></div>
                    </div>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_cart_to'); ?></div>
                        <div class="three-fourth"><?= $tpl['dropoff_arr']['location'] ?></div>
                    </div>
					<?php if(!$tpl['pickup_arr']['is_airport'] && !$tpl['dropoff_arr']['is_airport']) { ?>
						<div class="f-row">
                            <div class="one-fourth"><?php __('front_pickup_address'); ?></div>
                            <div class="three-fourth"><?= $tpl['arr']['c_address'] ?></div>
                        </div>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_dropoff_address'); ?></div>
                            <div class="three-fourth"><?= $tpl['arr']['c_destination_address'] ?></div>
                        </div>   
					<?php } else { ?>
	                    <?php if($tpl['pickup_arr']['is_airport']): ?>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_flight_number'); ?></div>
	                            <div class="three-fourth"><?= $tpl['arr']['c_flight_number'] ?></div>
	                        </div>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_airline_company'); ?></div>
	                            <div class="three-fourth"><?= $tpl['arr']['c_airline_company'] ?></div>
	                        </div>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_destination_address'); ?></div>
	                            <div class="three-fourth"><?= $tpl['arr']['c_destination_address'] ?></div>
	                        </div>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_hotel'); ?></div>
	                            <div class="three-fourth"><?= $tpl['arr']['c_hotel'] ?></div>
	                        </div>
	                    <?php else: ?>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_address'); ?></div>
	                            <div class="three-fourth"><?= $tpl['arr']['c_address'] ?></div>
	                        </div>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_hotel'); ?></div>
	                            <div class="three-fourth"><?= $tpl['arr']['c_hotel'] ?></div>
	                        </div>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_flight_departure_time'); ?></div>
	                            <div class="three-fourth"><?= $tpl['arr']['c_departure_flight_time'] ?></div>
	                        </div>
	                    <?php endif; ?>
					<?php } ?>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_passengers'); ?></div>
                        <div class="three-fourth"><?= $tpl['arr']['passengers'] ?></div>
                    </div>
                    <div class="f-row">
                        <div class="one-fourth"><?php __('front_vehicle'); ?></div>
                        <div class="three-fourth"><?= $tpl['fleet']['fleet'] ?></div>
                    </div>
                    <?php if(!empty($tpl['arr']['c_notes'])): ?>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_notes'); ?></div>
                            <div class="three-fourth"><?= nl2br($tpl['arr']['c_notes']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($tpl['return_arr'])): ?>
                        <h3><?php __('front_return_transfer_details'); ?></h3>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_date'); ?></div>
                            <div class="three-fourth"><?= date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['return_date'])) ?></div>
                        </div>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_time'); ?></div>
                            <div class="three-fourth"><?= date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['return_date'])) ?></div>
                        </div>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_cart_from'); ?></div>
                            <div class="three-fourth"><?= $tpl['dropoff_arr']['location'] ?></div>
                        </div>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_cart_to'); ?></div>
                            <div class="three-fourth"><?= $tpl['pickup_arr']['pickup_location'] ?></div>
                        </div>
						<?php if(!$tpl['pickup_arr']['is_airport'] && !$tpl['dropoff_arr']['is_airport']) { ?>
							<div class="f-row">
	                            <div class="one-fourth"><?php __('front_pickup_address'); ?></div>
	                            <div class="three-fourth"><?= $tpl['return_arr']['c_address'] ?></div>
	                        </div>
	                        <div class="f-row">
	                            <div class="one-fourth"><?php __('front_dropoff_address'); ?></div>
	                            <div class="three-fourth"><?= $tpl['return_arr']['c_destination_address'] ?></div>
	                        </div>
						<?php } else { ?>
	                        <?php if($tpl['pickup_arr']['is_airport']): ?>
	                            <div class="f-row">
	                                <div class="one-fourth"><?php __('front_address'); ?></div>
	                                <div class="three-fourth"><?= $tpl['return_arr']['c_address'] ?></div>
	                            </div>
	                            <div class="f-row">
	                                <div class="one-fourth"><?php __('front_flight_departure_time'); ?></div>
	                                <div class="three-fourth"><?= $tpl['return_arr']['c_departure_flight_time'] ?></div>
	                            </div>
	                        <?php else: ?>
	                            <div class="f-row">
	                                <div class="one-fourth"><?php __('front_flight_number'); ?></div>
	                                <div class="three-fourth"><?= $tpl['return_arr']['c_flight_number'] ?></div>
	                            </div>
	                            <div class="f-row">
	                                <div class="one-fourth"><?php __('front_airline_company'); ?></div>
	                                <div class="three-fourth"><?= $tpl['return_arr']['c_airline_company'] ?></div>
	                            </div>
	                        <?php endif; ?>
						<?php } ?>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_passengers'); ?></div>
                            <div class="three-fourth"><?= $tpl['return_arr']['passengers'] ?></div>
                        </div>
                        <div class="f-row">
                            <div class="one-fourth"><?php __('front_vehicle'); ?></div>
                            <div class="three-fourth"><?= $tpl['fleet']['fleet'] ?></div>
                        </div>
                        <?php if(!empty($tpl['arr']['c_notes'])): ?>
                            <div class="f-row">
                                <div class="one-fourth"><?php __('front_notes'); ?></div>
                                <div class="three-fourth"><?= nl2br($tpl['return_arr']['c_notes']) ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(!empty($tpl['extra_arr'])): ?>
                        <h3><?php __('front_extras'); ?></h3>
                        <?php foreach($tpl['extra_arr'] as $extra): ?>
                            <div class="f-row">
                                <div class="one-fourth">
                                    <?= $extra['name'] ?>
                                    <?php if(!empty($extra['info'])): ?>
                                        <i>(<?= $extra['info'] ?>)</i>
                                    <?php endif; ?>
                                </div>
                                <div class="three-fourth"><?= $extra['quantity'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if($tpl['arr']['discount'] > 0): ?>
                        <h3><?php __('front_discount_code'); ?></h3>
                        <div class="f-row">
                            <div class="one-fourth"><?= $tpl['arr']['voucher_code'] ?></div>
                            <div class="three-fourth"><?= number_format($tpl['arr']['discount'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></div>
                            <br />
                            <br />
                        </div>
                    <?php endif; ?>

                    <h3><?php __('front_payment_medthod'); ?></h3>
                    <div class="f-row">
                        <div class="one-fourth" style="width: 100%;"><?php __('payment_methods_ARRAY_' . $tpl['arr']['payment_method']) ?></div>
                        <br />
                        <br />
                    </div>

                    <div class="f-row">
                        <div class="one-half">
                            <h3><?php __('front_total'); ?>: <?= number_format($tpl['arr']['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></h3>
                            <br />
                        </div>
                
                    </div>
                </form>
                <div id="trBookingMsg_<?php echo $_GET['index'];?>" class="f-row">
                	<?php 
                	switch ($tpl['arr']['payment_method'])
					{
						case 'paypal':
							?><div class="trSystemMessage"><?php echo $front_messages[1]; ?></div><?php
							if (pjObject::getPlugin('pjPaypal') !== NULL)
							{
								$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
							}
							break;
						case 'authorize':
							?><div class="trSystemMessage"><?php echo $front_messages[2]; ?></div><?php
							if (pjObject::getPlugin('pjAuthorize') !== NULL)
							{
								$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
							}
							break;
						case 'saferpay':
							if ($controller->defaultPaySafePaymentMethod == 'direct') {
								if(!empty($tpl['arr']['txn_id']))
								{
									?>
									<div class="alert alert-success">
										<?php __('front_messages_ARRAY_10');?>		
									</div>
									<?php
								}else{
									$paysafe = $tpl['paysafe_data'];
									if(isset($paysafe['body']['RedirectUrl']))
									{
										$url = $paysafe['body']['RedirectUrl'];
										?>
										<div class="text-success">
											<h3><?php echo $front_messages[11]; ?></h3>
											<div><?php echo $front_messages[12]; ?></div>
										</div>
										<div id="trSaferpayForm_<?php echo $_GET['index'];?>">
											<iframe name="trSaferpay" id="trSaferpay_<?php echo $_GET['index'];?>" scrolling="no" src="<?php echo $url;?>" height="100%" width="100%" style="min-height: 760px;"></iframe>
										</div>
										<?php
									} else { ?>
										<div class="text-success text-danger">
											<p><?php echo $front_messages[7]; ?></p>
											<?php 
											if (isset($paysafe['body']['ErrorDetail'])) {
												foreach ($paysafe['body']['ErrorDetail'] as $paysafe_err) {
													?>
													<p><?php echo $paysafe_err;?></p>
													<?php 	
												}
											} elseif (isset($paysafe['body']['ErrorMessage'])) {
												?>
												<p><?php echo $paysafe['body']['ErrorMessage'];?></p>
												<?php 
											}
											?>
										</div>
									<?php }
								}
							}
						break;
					}
                	?>
                </div>
            </div>
        </div>
    </div>
</main>