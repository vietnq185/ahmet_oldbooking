<?php
$index = pjObject::escapeString($_GET['index']);
?>

<main class="main" role="main" id="trContainer_<?php echo $index;?>">
    <div class="advanced-search color">
        <div class="wrap">
            <form id="trSearchForm_<?php echo $index;?>" action="" method="post">
                <input type="hidden" name="index" value="<?= $index ?>">
                <input type="hidden" name="autoload_next_step" id="autoloadNextStep_<?php echo $index;?>" value="<?= (int) @$tpl['search_post']['autoload_next_step']; ?>"/>
                <!-- Row -->
                <div class="f-row">
                    <div class="form-group select one-half" style="position: relative;">
                        <label><?php __('front_from'); ?></label><br/>
                        <i class="fas fa-map-marker-alt" style="left: 0;"></i>
                        <select name="location_id" id="trLocationId_<?php echo $index;?>" class="select2 required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                            <option value=""><?php __('front_choose', false, false); ?></option>
                            <?php
                            foreach($tpl['pickup_arr'] as $k => $v)
                            {
                                ?><option value="<?php echo $v['id'];?>"<?php echo isset($tpl['search_post']) ? ($tpl['search_post']['location_id'] == $v['id'] ? ' selected="selected"' : null) : null;?> data-icon="image-<?php echo $v['icon']; ?>"><?php echo $v['pickup_location'];?></option><?php
                            }
                            ?>

                        </select>
                       
                    </div>
                    
                    <div class="form-group select one-half" style="position: relative;">
                        <label><?php __('front_to'); ?></label><br/>
                        <i class="fas fa-map-marker-alt" style="left: 0;"></i>
                        <span id="dropoffBox_<?php echo $index;?>" class="trContent">
                            <select name="dropoff_id" id="trDropoffId_<?php echo $index;?>" class="select2 required trLoadPrices" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                                <option value=""><?php __('front_choose', false, false); ?></option>
                                <?php
                                if(isset($tpl['search_post']))
                                {
                                    foreach($tpl['dropoff_arr'] as $k => $v)
                                    {
                                        ?><option value="<?php echo $v['id'];?>"<?php echo isset($tpl['search_post']) ? ($tpl['search_post']['dropoff_id'] == $v['id'] ? ' selected="selected"' : null) : null;?> data-icon="image-<?php echo $v['icon']; ?>"><?php echo $v['location'];?></option><?php
                                    }
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    </div>
                     <div class="f-row">
                     <div class="form-group select one-fourth">
                       <label><?php __('front_passengers'); ?></label><br/>
                       <i class="fas fa-user" style="left: 0;"></i>
                        <select name="passengers_from_to" class="required trLoadPrices" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
                            <?php /*<option value=""><?php __('front_passengers', false, false); ?></option> */?>
                            <?php foreach($tpl['passenger_range_arr'] as $v): ?>
                                <option value="<?= $v ?>"<?php echo isset($tpl['search_post']) ? ($tpl['search_post']['passengers_from_to'] == $v ? ' selected="selected"' : null) : null;?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group datepicker one-fourth" style="position: relative;">
                        <label for="trDate_<?php echo $index?>"><?php __('front_departure'); ?></label><br/>
                        <i class="far fa-calendar-alt" style="left: 0;"></i>
                        <input type="text" placeholder="<?php __('front_select_transfer_date', false, true); ?>" id="trDate_<?php echo $index?>" name="date" readonly value="<?php echo isset($tpl['search_post']) && isset($tpl['search_post']['date']) ? htmlspecialchars($tpl['search_post']['date']) : null; ?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
                    </div>                   

                    <div class="form-group one-half trBtnSearchWrapper">
                    	<label>&nbsp;</label>
                        <button type="submit" class="btn large black"><?php __('front_search'); ?></button>
                        <p class="trCheckErrorMsg"></p>
                    </div>

                </div>
                <!--// Row -->
            </form>
        </div>
    </div>

    <div class="wrap">
        <div id="trBookingStep_Services"></div>
        <div id="trBookingStep_TransferType"></div>
        <div id="trBookingStep_Extras"></div>
        <div id="trBookingStep_Departure"></div>
        <div id="trBookingStep_Return"></div>
        <div id="trBookingStep_Passenger"></div>
        <div id="trBookingStep_Checkout"></div>
    </div>
</main>