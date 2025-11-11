<?php
$STORE = @$tpl['store'];
$date = pjUtil::formatDate($STORE['search']['date'], $tpl['option_arr']['o_date_format']);	
?>
<div class="row">
    <!--- Content -->
    <div class="full-width content">
        <div class="results">
            <header class="f-title color"><?= str_replace('{X}', 1, __('front_step_x', true, false)) ?><?php __('front_step_choose_vehicle'); ?></header>

   

            <?php
            if($tpl['status'] == 'OK')
            {
            	?>
<div class="vc_message_box vc_message_box-standard vc_message_box-square vc_color-alert-success"><div class="vc_message_box-icon"><i class="fas fa-check-circle"></i></div><p><?php __('front_free_cancellation_msg');?></p>
</div>
            	<?php
                foreach($tpl['fleet_arr'] as $k => $v)
                {
                	$one_way_price = $v['price'];
            		$return_price = $v['price'];
                	$fleet_discount_arr = $controller->getFleetDiscount($date, $v['id'], $tpl['price_level']);
                	if ($fleet_discount_arr) {
						if ($fleet_discount_arr['is_subtract'] == 'T') {
							if ($fleet_discount_arr['type'] == 'amount') {
								$one_way_price = $one_way_price - $fleet_discount_arr['discount'];
							} else {
								$one_way_price = $one_way_price - (($one_way_price * $fleet_discount_arr['discount']) / 100);
							}
						} else {
							if ($fleet_discount_arr['type'] == 'amount') {
								$one_way_price = $one_way_price + $fleet_discount_arr['discount'];
							} else {
								$one_way_price = $one_way_price + (($one_way_price * $fleet_discount_arr['discount']) / 100);
							}
						}
						if ($one_way_price < 0) {
							$one_way_price = 0;
						}
					}
					$one_way_price = round($one_way_price);
					$return_price = round($return_price);
                    $result = pjUtil::calPrice($one_way_price, $return_price, 0, 0, $tpl['option_arr'], '', '');
                    $price = $result['total'];
                    $thumb_url = !empty($v['thumb_path'])? PJ_INSTALL_URL . $v['thumb_path']: PJ_INSTALL_URL . PJ_IMG_PATH . 'uploads/img.jpg';
                    ?>
                    <article class="result">
                    	<div class="traveller-choice-img"><img src="<?php echo PJ_INSTALL_URL;?>app/web/img/frontend/TC_LL.svg"/></div>
                    	<div class="third-fourth">
	                        <div class="one-fourth heightfix p25 text-center">
	                        	<img src="<?php echo $thumb_url;?>" alt="" />
	                        </div>
	                        <div class="one-half heightfix p75">
	                            <h3>
	                            	<?php echo pjSanitize::clean($v['fleet']);?>
	                            	<?php if (!empty($v['badget'])) { ?>
	                            	<span class="badget"><?php echo pjSanitize::clean($v['badget']);?></span>
	                            	<?php } ?>
	                            </h3>
	                       		<ul>
	                                <li>
	                                    <p>
		                                    <i class="fas fa-user"></i> <?= str_replace('{NUMBER}', $v['passengers'], __('front_max_passengers', true, false)) ?>
	                                    </p>
	                                    <p>
	                                    	<i class="fas fa-route"></i> <?= str_replace('{NUMBER}', $v['distance'], __('front_estimated_distance', true, false)) ?>
	                                    </p>
	                                    <p>
	                                    	<i class="far fa-clock"></i> <?= str_replace('{NUMBER}', $v['duration'], __('front_estimated_time', true, false)) ?>
	                                    </p>
	                                </li>
	                                <li>
	                                    <p>
		                                    <i class="far fa-check-circle"></i> <?php __('front_no_credit_card_fee');?>
	                                    </p>
	                                    <p>
		                                    <i class="far fa-check-circle"></i> <?php __('front_free_cancellation_wt');?>
	                                    </p>
	                                    <p>
		                                    <i class="far fa-check-circle"></i> <?php __('front_meet_freet_service');?>
	                                    </p>
	                                </li>
	                            </ul>
								<a href="javascript:void(0)" class="trigger" title="Read more"><span><?php __('front_more_info');?></span></a>
	                        </div>
	                        <br class="clar-both">
	                       
	                    </div>
                        <div class="one-fourth heightfix text-center">
                            <div>
                                <?php if($tpl['no_date_selected']): ?>
                                	<div class="price-info">
                                    	<span class="meta selectdate"><p><i class="fas fa-calendar"></i> </p><?php __('front_price_not_available_text')?></span>
                                    </div>
                                    <a href="#" class="btn grey large trChooseDateButton"><?php __('front_btn_choose_date', false, false);?></a>
                                <?php else: ?>
                                	<div class="price-info">
	                                    <div class="prices">
	                                    	<?php if (!empty($v['crossedout_price']) || true): ?>
		                                        <div class="crossed-out-price">
		                                            <?php
		                                                $crossedOutPrice = ($v['crossedout_type'] == 'percent') ? round($price + ($price * $v['crossedout_price'] / 100), 2) : $v['crossedout_price'];
		                                                echo number_format(round($crossedOutPrice), 2, ',', ' ')
		                                            ?>
		                                            <small><?= $tpl['option_arr']['o_currency'] ?></small>
		                                        </div>
		                                    <?php endif; ?>	
		                                    <div class="price"><?php echo number_format($price, 2, ',', ' ');?> <small><?= $tpl['option_arr']['o_currency'] ?></small></div>
	                                    </div>
	                                    <span class="meta"><?php __('front_all_inclusive')?></span>
	                                    <p class="badget-free-cancellation">
		                                    <i class="far fa-check-circle"></i> <?php __('front_badget_free_cancellation');?>
	                                    </p>
	                                </div>
                                    <a href="#" class="btn grey large trChooseVehicleButton " data-id="<?php echo $v['id'];?>"><i class="fas fa-check-circle"></i><span><?php __('front_btn_select', false, false);?></span></a>
                                <?php endif; ?>
                            </div>
							</div>

                            <?php if ((float)$v['return_discount'] > 0): ?>
	                            <div class="discount-info">
	                            	<div class="full-width information2">
		                                <span class="return-discount"><?php echo sprintf(__('front_return_discount_info', true), (float)$v['return_discount'].'%');?></span>
		                            </div>
		                            <br class="clar-both">
	                            </div>
	                        <?php endif; ?>        
                        <div class="vehicle-description information full-width">
                            <a href="javascript:void(0)" class="close color" title="Close">x</a>
                            <p><?php echo $v['description']; ?></p>
                       </div>
                    </article>
                    <?php
                }
            }else{
                ?>
                <div class="trSystemMessage"><?php __('front_error'); ?></div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

