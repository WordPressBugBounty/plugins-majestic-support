<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
    	<?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('satisfaction_reports'); ?>
    	<?php
		$MJTC_percentage = round(majesticsupport::$_data[0]['avg']*20,2);
		?>
		<div id="msadmin-data-wrp">
			<div class="ms-statifacetion-report-wrapper" >
				<div class="statifacetion-report-left" >
					<?php
						$MJTC_class="first";
						$MJTC_src ="excelent.png";
						if($MJTC_percentage > 80){
							$MJTC_class="first";
							$MJTC_src ="excelent.png";
						}elseif($MJTC_percentage > 60){
							$MJTC_class="second";
							$MJTC_src ="happy.png";
						}elseif($MJTC_percentage > 40){
							$MJTC_class="third";
							$MJTC_src ="normal.png";
						}elseif($MJTC_percentage > 20){
							$MJTC_class="fourth";
							$MJTC_src ="bad.png";
						}elseif($MJTC_percentage > 0){
							$MJTC_class="fifth";
							$MJTC_src ="angery.png";
						}

						?>
					<div class="top-number <?php echo esc_attr($MJTC_class);?>" >
						<?php echo esc_html($MJTC_percentage).'%'; ?>
					</div>
					<span class="total-feedbacks" >
						<?php echo esc_html(__('Based on','majestic-support')).'&nbsp;'. esc_html(majesticsupport::$_data[0]['result'][6]).'&nbsp;'. esc_html(__('Feedback','majestic-support'));?>
					</span>
					<div class="top-text" >
						<?php echo esc_html(__('Customer Satisfaction','majestic-support')); ?>
					</div>
				</div>

				<div class="satisfaction-report-right <?php echo esc_attr($MJTC_class); ?>" >
					<img alt="<?php echo esc_attr(__('satisfaction image','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/<?php echo esc_attr($MJTC_src);?>" />
				</div>




				<div class="ms-satisfaction-report-bottom" >
					<div class="indi-stats first" >
						<img alt="<?php echo esc_attr(__('Excellent','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/excelent.png" />
						<div class="stats-percentage" ><?php
							if(majesticsupport::$_data[0]['result'][6] != 0){
								echo esc_html(round(majesticsupport::$_data[0]['result'][5]/majesticsupport::$_data[0]['result'][6]*100 ,2).'%');
							}else{
								echo esc_html(__('NA','majestic-support'));
							}
							?></div>
						<div class="stats-text" > <?php echo esc_html(__('Excellent','majestic-support')); ?> </div>
					</div>
					<div class="indi-stats second" >
						<img alt="<?php echo esc_attr(__('Happy','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/happy.png" />
						<div class="stats-percentage" ><?php
							if(majesticsupport::$_data[0]['result'][6] != 0){
								echo esc_html(round(majesticsupport::$_data[0]['result'][4]/majesticsupport::$_data[0]['result'][6]*100 ,2).'%');
							}else{
								echo esc_html(__('NA','majestic-support'));
							}
							?></div>
						<div class="stats-text" > <?php echo esc_html(__('Happy','majestic-support')); ?> </div>
					</div>
					<div class="indi-stats third" >
						<img alt="<?php echo esc_attr(__('Normal','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/normal.png" />
						<div class="stats-percentage" ><?php
							if(majesticsupport::$_data[0]['result'][6] != 0){
								echo esc_html(round(majesticsupport::$_data[0]['result'][3]/majesticsupport::$_data[0]['result'][6]*100 ,2).'%');
							}else{
								echo esc_html(__('NA','majestic-support'));
							}
							?></div>
						<div class="stats-text" > <?php echo esc_html(__('Normal','majestic-support')); ?> </div>
					</div>
					<div class="indi-stats fourth" >
						<img alt="<?php echo esc_attr(__('bad','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/bad.png" />
						<div class="stats-percentage" ><?php
							if(majesticsupport::$_data[0]['result'][6] != 0){
								echo esc_html(round(majesticsupport::$_data[0]['result'][2]/majesticsupport::$_data[0]['result'][6]*100 ,2).'%');
							}else{
								echo esc_html(__('NA','majestic-support'));
							}
							?></div>
						<div class="stats-text" > <?php echo esc_html(__('Sad','majestic-support')); ?> </div>
					</div>
					<div class="indi-stats fifth" >
						<img alt="<?php echo esc_attr(__('Angry','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/angery.png" />
						<div class="stats-percentage" ><?php
							if(majesticsupport::$_data[0]['result'][6] != 0){
								echo esc_html(round(majesticsupport::$_data[0]['result'][1]/majesticsupport::$_data[0]['result'][6]*100 ,2).'%');
							}else{
								echo esc_html(__('NA','majestic-support'));
							}
							?></div>
						<div class="stats-text" > <?php echo esc_html(__('Angry','majestic-support')); ?> </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
