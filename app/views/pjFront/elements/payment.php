<?php
$index = pjObject::escapeString($_GET['index']);
$payment_methods = __('payment_methods', true);
$payment_methods_desc = __('payment_methods_desc', true);
$pm_sort_arr = array('cash','creditcard_later');
$cartIndex = str_replace('pjAction', '', $_GET['action']) . '_' . pjObject::escapeString($_GET['index']);
?>
<main class="main booking" role="main">
	<br/>
	<br/>
    <div class="wrap">
        <div class="pjWrapperShuttleBooking">
        	<div class="trPaymentContainer container-fluid">
        		<div class="row">
        		    <div class="full-width content">
        		        <header class="f-title color"><?php __('front_payment_title'); ?></header>
        		        
        		    </div>
        		
        		    <div class="three-fourth">
        		        <?php if($tpl['status'] == 'OK'): ?>
        		            <form id="trPaymentForm_<?php echo $index;?>" class="trPaymentForm" action="" method="post">
        		                <input type="hidden" name="step_payment" value="1"/>
        						<input type="hidden" name="booking_id" value="<?php echo $tpl['arr']['id'];?>"/>
        						
        						<?php if($tpl['option_arr']['o_deposit_payment'] > 0) { ?>
        							<?php if ((float)$tpl['option_arr']['o_deposit_payment'] >= 100) { ?>
        								<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-info"><div class="vc_message_box-icon"><i class="fa fa-info-circle"></i></div><p> <?php echo in_array($tpl['arr']['payment_method'], array('saferpay', 'creditcard')) ? '' : 'none';?><?php __('front_full_price_charged_desc');?></p></div>
        							<?php } else { ?>
        								<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-info"><div class="vc_message_box-icon"><i class="fa fa-info-circle"></i></div><p><?= str_replace('{X}', (float) $tpl['option_arr']['o_deposit_payment'], __('front_deposit_payment_in_advance', true)); ?></p></div>
        							<?php } ?>
        						<?php } ?>
        						
        						<div class="trSaferpayWrap">
        							<div class="row">
        								<div class="col-xs-12">
        									<?php 
        									if(!empty($tpl['arr']['txn_id']))
        									{
        										?>
        										<div class="alert alert-success d-flex align-items-center">
        											<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><?php __('front_messages_ARRAY_10');?></span>		
        										</div>
        										<?php
        									}else{
        										$paysafe = $tpl['paysafe_data'];
        										if(isset($paysafe['body']['RedirectUrl']))
        										{
        											$url = $paysafe['body']['RedirectUrl'];
        											?>
        											<div id="trSaferpayForm_<?php echo $_GET['index'];?>">
        												<iframe name="trSaferpay" id="trSaferpay_<?php echo $_GET['index'];?>" class="trSaferpayIframe" scrolling="no" src="<?php echo $url;?>" height="100%" width="100%" style="min-height: 450px;"></iframe>
        											</div>
        											<?php
        										} else { ?>
        											<div class="alert alert-warning d-flex align-items-center">
        												<i class="fa-solid fa-triangle-exclamation"></i>
        												<span class="alert-desc">
        													<?php echo $front_messages[7]; ?>
        													<?php 
        													if (isset($paysafe['body']['ErrorDetail'])) {
        														foreach ($paysafe['body']['ErrorDetail'] as $paysafe_err) {
        															?>
        															<br/><?php echo $paysafe_err;?>
        															<?php 	
        														}
        													} elseif (isset($paysafe['body']['ErrorMessage'])) {
        														?>
        														<br/><?php echo $paysafe['body']['ErrorMessage'];?>
        														<?php 
        													}
        													?>
        												</span>		
        											</div>
        										<?php }
        									}
        									?>
        								</div>
        							</div>					
        						</div>
        		                
        		        		<?php if($tpl['option_arr']['o_payment_disable'] == 'No' && empty($tpl['arr']['txn_id']) && !isset($tpl['arr']['allow_saferpay_only'])): 
        		        		    $payment_methods = __('payment_methods', true);
        		        		    $total = round($tpl['arr']['total'] - $tpl['arr']['credit_card_fee']);
        		        		    $deposit = in_array($tpl['arr']['payment_method'], array('creditcard', 'paypal', 'authorize', 'saferpay')) || is_null($tpl['arr']['payment_method']) ? (($total * $tpl['option_arr']['o_deposit_payment']) / 100): 0;
        		        		    $deposit = round($deposit);
        		        		    ?>
        		                	<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-info"><div class="vc_message_box-icon"><i class="fa fa-info-circle"></i></div><p>
        							<?php echo __('front_select_payment_options_desc'); ?></p></div>		
        		                	<div class="payment-methods">
        		                		<?php foreach($pm_sort_arr as $k): ?>
        									<?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes'): 
            									$cc_fee = 0;
            									if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) {
            									    $cc_fee = round(($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100);
            									} else if ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) {
            									    $cc_fee = round(($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100);
            									}
        									   ?>
        										<div class="payment-method">
        											<div class="payment-method-info">
        												<h3><?php echo @$payment_methods[$k];?>  <?php echo number_format($total + $cc_fee, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></h3>
        												<div class="payment-method-desc"><?php echo @$payment_methods_desc[$k];?></div>
        											</div>
        											<div class="payment-method-selector">
        												<input type="radio" id="payment_method_<?php echo $k;?>" name="radio_payment_method" class="trPaymentMethodSelector" value="<?php echo $k;?>">
        											</div>
        											<br class="clear-both"/>
        										</div>
        									<?php endif; ?>
        								<?php endforeach; ?>
        		                	</div>
        		                	
        		                    <div class="f-row" style="display:none;">
        		                        <div class="one-half">
        		                            <label for="payment_method"><?php __('front_payment_medthod'); ?></label>
        		                            <select name="payment_method" id="trPaymentMethod_<?php echo $index;?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
        		                                <option value="" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>"><?php __('front_choose', false, false); ?></option>
        		                                <?php foreach(__('payment_methods', true, false) as $k => $v): ?>
    			                                    <?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes'): ?>
    			                                    	<?php if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) { ?>
    		                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_creditcard_later_fee'].'%', number_format(round(($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($deposit + round((($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($total + round((($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
        		                                    	<?php } elseif ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) { ?>
        		                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_saferpay_fee'].'%', number_format(round(($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($deposit + round((($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($total + round((($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
        		                                    	<?php } else { ?>
        		                                        	<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="" data-deposit="<?php echo number_format($deposit, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($total, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
        		                                        <?php } ?>
    			                                    <?php endif; ?>
    			                                <?php endforeach; ?>
        		                            </select>
        		                        </div>
        		                    </div>
        		                    
        		                    <div id="trCCData_<?php echo $index;?>" style="display: <?php echo isset($FORM['payment_method']) && ($FORM['payment_method'] == 'creditcard' || ($FORM['payment_method'] == 'saferpay' && $controller->defaultPaySafePaymentMethod == 'direct')) ? 'block' : 'none'; ?>">
        		                        <div class="f-row">
        		                            <div class="one-half">
        		                                <label for="cc_owner"><?php __('front_cc_owner'); ?></label>
        		                                <input type="text" name="cc_owner" class="required" value="<?php echo isset($FORM['cc_owner']) ? pjSanitize::clean($FORM['cc_owner']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
        		                            </div>
        		
        		                            <div class="one-half">
        		                                <label for="cc_num"><?php __('front_cc_num'); ?></label>
        		                                <input type="text" name="cc_num" class="required" value="<?php echo isset($FORM['cc_num']) ? pjSanitize::clean($FORM['cc_num']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
        		                            </div>
        		                        </div>
        		
        		                        <div class="f-row">
        		                            <div class="one-fourth">
        		                                <label for="cc_exp_month"><?php __('front_cc_exp'); ?> (<?php __('front_month'); ?>)</label>
        		                                <select name="cc_exp_month" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_exp_month', true, false));?>">
        		                                    <option value="">-- <?php __('front_choose', false, false); ?> --</option>
        		                                    <?php for($i = 1; $i <= 12; $i++): ?>
        		                                        <option value="<?= $i ?>"<?php echo (int) @$FORM['cc_exp_month'] == $i ? ' selected="selected"' : NULL; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
        		                                    <?php endfor; ?>
        		                                </select>
        		                            </div>
        		
        		                            <div class="one-fourth">
        		                                <label for="cc_exp_year"><?php __('front_cc_exp'); ?> (<?php __('front_year'); ?>)</label>
        		                                <select name="cc_exp_year" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_exp_year', true, false));?>">
        		                                    <option value="">-- <?php __('front_choose', false, false); ?> --</option>
        		                                    <?php $y = (int) date('Y'); ?>
        		                                    <?php for($i = $y; $i <= $y + 10; $i++): ?>
        		                                        <option value="<?= $i ?>"<?php echo @$FORM['cc_exp_year'] == $i ? ' selected="selected"' : NULL; ?>><?= $i ?></option>
        		                                    <?php endfor; ?>
        		                                </select>
        		                            </div>
        		
        		                            <div class="one-sixth">
        		                                <label for="cc_code"><?php __('front_cc_code'); ?></label>
        		                                <input type="text" name="cc_code" class="required" value="<?php echo isset($FORM['cc_code']) ? pjSanitize::clean($FORM['cc_code']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
        		                            </div>
        		                        </div>
        		
        		                        <?php if($tpl['option_arr']['o_deposit_payment'] > 0): ?>
        		                            <div class="f-row">
        		                                <?= str_replace('{X}', (float) $tpl['option_arr']['o_deposit_payment'], __('front_deposit_payment_in_advance', true)); ?>
        		                            </div>
        		                        <?php endif; ?>
        		                    </div>
        		                <?php endif; ?>                
        		
        		                <div class="actions">
        		                    <button type="button" class="btn medium color right btnFinishBooking" style="display: none;" data-id="<?php echo $tpl['arr']['id'];?>"><?php __('front_btn_finish_your_booking'); ?></button>
        		                </div>
        		                
        		                <div id="trBookingMsg_<?php echo $index?>" class="f-row" style="display: none;">
        		                    <div class="alert"></div>
        		                </div>
        		            </form>
        		        <?php else: ?>
        		            <div class="trSystemMessage">
        		                <?php __('front_error'); ?>
        		            </div>
        		        <?php endif; ?>
        		    </div>
        			<?php if($tpl['status'] == 'OK') { ?>
        			    <aside id="trCart_<?php echo $cartIndex;?>" class="one-fourth sidebar right trCartInfo">
        				    <div class="widget">
        				        <h4><?php __('front_booking_summary'); ?></h4>
        				        <div class="summary">
        				            <div>
        				                <h5><?php __('front_cart_departure'); ?></h5>
        				                <dl>
        				                    <dt class="trCartDepartureDate"><?php __('front_date'); ?></dt>
        				                    <dd class="trCartDepartureDate"><?= date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['booking_date'])) ?></dd>
        				
        			                        <dt class="trCartDepartureTime"><?php __('front_time'); ?></dt>
        			                        <dd class="trCartDepartureTime"><?= date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date'])) ?></dd>
        				
        				                    <dt><?php __('front_cart_from'); ?></dt>
        				                    <dd><?= $tpl['pickup_arr']['pickup_location'] ?></dd>
        				
        				                    <dt><?php __('front_cart_to'); ?></dt>
        				                    <dd><?= $tpl['dropoff_arr']['location'] ?></dd>
        				
        				                    <dt class="trCartPax"><?php __('front_cart_passengers'); ?></dt>
        				                    <dd class="trCartPax"><?= $tpl['arr']['passengers'] ?></dd>
        				
        				                    <dt><?php __('front_vehicle'); ?></dt>
        				                    <dd><?= $tpl['fleet']['fleet'] ?></dd>
        									 <?php if(!empty($tpl['extra_arr'])): ?>
        					                    <dt class="trCartExtras"><?php __('front_extras'); ?></dt>
        					                    <dd class="trCartExtras"><?php foreach($tpl['extra_arr'] as $extra): ?>
													
        					                    			<?= $extra['quantity'] ?> x <?= $extra['name'] ?><br/>
        				                                    <?php if(!empty($extra['info'])): ?>
        				                                    <?php endif; ?>
        					                    	<?php endforeach; ?>
        					                    </dd>
        					            	 <?php endif; ?>
        				                </dl>
        				            </div>
        				
        				            <?php if(!empty($tpl['return_arr'])): ?>
        				                <div>
        				                    <h5><?php __('front_cart_return'); ?></h5>
        				                    <dl>
        			                            <dt class="trCartReturnDate"><?php __('front_date'); ?></dt>
        			                            <dd class="trCartReturnDate"><?= date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['return_date'])) ?></dd>
        			
        			                            <dt class="trCartReturnTime"><?php __('front_time'); ?></dt>
        			                            <dd class="trCartReturnTime"><?= date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['return_date'])) ?></dd>
        				                        
        				
        				                        <dt><?php __('front_cart_from'); ?></dt>
        				                        <dd><?= $tpl['dropoff_arr']['location'] ?></dd>
        				                        <dt><?php __('front_cart_to'); ?></dt>
        				                        <dd><?= $tpl['pickup_arr']['pickup_location'] ?></dd>
        				                    </dl>
        				                </div>
        				            <?php endif; ?>
        				
        							<?php 
    						        $payment_methods = __('payment_methods', true);
    					        	$deposit = $tpl['arr']['deposit'];
    			                    $rest = $tpl['arr']['total'] - $deposit;
    					        	?>
        			                <div>
        			                	<h5><?php __('front_payment'); ?></h5>
        			                	
        			                    <dl>
            			                     <span>
    					                		<?php if ($tpl['arr']['payment_method'] == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) { ?>
    					                			<span class="pjSbCartPaymentMethod"><?php echo @$payment_methods[$tpl['arr']['payment_method']]; ?><br/><?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_creditcard_later_fee'].'%', number_format($tpl['arr']['credit_card_fee'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?></span>
    					                		<?php } elseif ($tpl['arr']['payment_method'] == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) { ?>
    					                			<span class="pjSbCartPaymentMethod"><?php echo @$payment_methods[$tpl['arr']['payment_method']]; ?><br/><?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_saferpay_fee'].'%', number_format($tpl['arr']['credit_card_fee'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?></span>
    					                		<?php } else { ?>
    					                			<span class="pjSbCartPaymentMethod"><?php echo @$payment_methods[$tpl['arr']['payment_method']]; ?></span>
    					                		<?php } ?>
    					                		<?php if($tpl['option_arr']['o_deposit_payment'] > 0) { ?>
    					                			<span class="pjSbFullPriceChargedDesc" style="display: <?php echo in_array($tpl['arr']['payment_method'], array('saferpay', 'creditcard')) ? '' : 'none';?>">
    						                            <br/><?php __('front_now_to_pay'); ?>: <span class="pjSbCartDeposit"><?php echo number_format($deposit, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
    						                            <br/><?php __('front_rest_to_pay'); ?>: <span class="pjSbCartRest"><?php echo number_format($rest, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
    					                            </span>
    					                        <?php } ?>
    					                	</span>
    					                	<?php if($tpl['arr']['discount'] > 0) { ?>
    					                		<span>
						                            <br/><?php __('front_discount'); ?>: <span><?php echo number_format($tpl['arr']['discount'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
					                            </span>
        			                        <?php } ?>
    					                	<?php /*
        			                        <dt class="trCartPaymentMethod_<?php echo $cartIndex;?>" style="display: <?= !empty($tpl['arr']['payment_method'])? 'block': 'none' ?>;"><?php __('front_payment'); ?></dt>
        			                        <dd class="trCartPaymentMethod_<?php echo $cartIndex;?>" style="display: <?= !empty($tpl['arr']['payment_method'])? 'block': 'none' ?>;"><?php __("payment_methods_ARRAY_{$tpl['arr']['payment_method']}") ?></dd>
        			                        <?php if($tpl['arr']['discount'] > 0) { ?>
        				                        <dt class="trCartDiscount_<?php echo $cartIndex;?>" style="display: <?= !empty($tpl['arr']['discount'])? 'block': 'none' ?>;"><?php __('front_discount'); ?></dt>
        				                        <dd class="trCartDiscount_<?php echo $cartIndex;?>" style="display: <?= !empty($tpl['arr']['discount'])? 'block': 'none' ?>;"><?= number_format($tpl['arr']['discount'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></dd>
        			                        <?php } ?>
        			                        <?php if($tpl['option_arr']['o_deposit_payment'] > 0): ?>
        			                            <?php
        			                            $deposit = $tpl['arr']['total'] * $tpl['option_arr']['o_deposit_payment'] / 100;
        			                            $rest = $tpl['arr']['total'] - $deposit;
        			                            ?>
        			                            <dt class="trCartDeposit_<?php echo $cartIndex;?>" ><?php __('front_deposit'); ?></dt>
        			                            <dd class="trCartDeposit_<?php echo $cartIndex;?>" ><?= number_format($tpl['arr']['deposit'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></dd>
        			                            <dt class="trCartRest_<?php echo $cartIndex;?>" ><?php __('front_rest'); ?></dt>
        			                            <dd class="trCartRest_<?php echo $cartIndex;?>" ><?= number_format($rest, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></dd>
        			                        <?php endif; ?>
        			                         */?>
        			                    </dl>
        			                   
        			                </div>
        				
        				            <dl class="total pjSbTotalPrice">
        				                <dt><?php __('front_total'); ?></dt>
        				                <dd>
        				                	<span class="pjSbCartTotal"><?= number_format($tpl['arr']['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></span>
        				                	<small><?php __('front_total_price_all_inclusive');?></small>
        				                </dd>
        				            </dl>
        				        </div>
        				    </div>
        				</aside>
        			<?php } ?>
        		</div>
        	</div>
        </div>
	</div>
</main>	     