<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$MJTC_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');
wp_enqueue_style('majesticsupport-status-graph', MJTC_PLUGIN_URL . 'includes/css/status_graph.css', array(), '1.0.0');
wp_enqueue_script('majesticsupport-google-charts', MJTC_PLUGIN_URL . 'includes/js/google-charts.js', array(), '1.0.0', true);
wp_register_script( 'majesticsupport-google-charts-handle', false, array(), '1.0.0', true );
wp_enqueue_script( 'majesticsupport-google-charts-handle' );
$mjtc_scriptdateformat = MJTC_includer::MJTC_getModel('majesticsupport')->MJTC_getDateFormat();
$majesticsupport_js ="
    jQuery(document).ready(function ($) {
        $('.custom_date').datepicker({
            dateFormat: '". esc_html($mjtc_scriptdateformat)."'
        });
        google.load('visualization', '1', {packages:['corechart']});
		google.setOnLoadCallback(drawChart);
	});

	function drawChart() {
      	var data = new google.visualization.DataTable();
		data.addColumn('date', '". esc_html(__('Dates','majestic-support'))."');
        data.addColumn('number', '". esc_html(__('New','majestic-support'))."');
        data.addColumn('number', '". esc_html(__('Answered','majestic-support'))."');
        data.addColumn('number', '". esc_html(__('Pending','majestic-support'))."');
        data.addColumn('number', '". esc_html(__('Overdue','majestic-support'))."');
        data.addColumn('number', '". esc_html(__('Closed','majestic-support'))."');
		data.addRows([
			". majesticsupport::$_data['line_chart_json_array']."
        ]);

        var options = {
          colors:['#159667','#2168A2','#f39f10','#B82B2B','#3D355A'],
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 6,
		  // This line will make you select an entire row of data at a time
		  focusTarget: 'category',
		  chartArea: {width:'90%',top:50}
		};

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
    }
";
wp_add_inline_script('majesticsupport-google-charts-handle',$majesticsupport_js);
$majesticsupport_js ="
	function resetFrom(){
		document.getElementById('date_start').value = '';
		document.getElementById('date_end').value = '';
		document.getElementById('majesticsupportform').submit();
	}

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
MJTC_message::MJTC_getMessage();
$MJTC_t_name = 'getstaffmemberexportbystaffid';
$MJTC_link_export = admin_url('admin.php?page=majesticsupport_export&task='.esc_attr($MJTC_t_name).'&action=mstask&uid='.esc_attr(majesticsupport::$_data['filter']['uid']).'&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']));
$MJTC_show_flag = 0;
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
    	<?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('agentdetail_reports'); ?>
        <div id="msadmin-data-wrp">
		    <?php
			$MJTC_agent = majesticsupport::$_data['staff_report'];
			?>
			<a href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_reports&mjslay=staffdetailreport&id='.esc_attr($MJTC_agent->id).'&date_start='.esc_attr(majesticsupport::$_data['filter']['date_start']).'&date_end='.esc_attr(majesticsupport::$_data['filter']['date_end']))); ?>"></a>
			<form class="mjtc-filter-form mjtc-report-form" name="majesticsupportform" id="majesticsupportform" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_reports&mjslay=staffdetailreport&id=".esc_attr(majesticsupport::$_data['staff_report']->id)),"staff-detail-report")); ?>">
			    <?php
			        $MJTC_curdate = date_i18n('Y-m-d');
			        $MJTC_enddate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
			        $MJTC_date_start = !empty(majesticsupport::$_data['filter']['date_start']) ? majesticsupport::$_data['filter']['date_start'] : $MJTC_curdate;
			        $MJTC_date_end = !empty(majesticsupport::$_data['filter']['date_end']) ? majesticsupport::$_data['filter']['date_end'] : $MJTC_enddate;
			    	echo wp_kses(MJTC_formfield::MJTC_text('date_start', date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)), array('class' => 'custom_date mjtc-form-date-field','placeholder' => esc_html(__('Start Date','majestic-support')))), MJTC_ALLOWED_TAGS);
			    	echo wp_kses(MJTC_formfield::MJTC_text('date_end', date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)), array('class' => 'custom_date mjtc-form-date-field','placeholder' => esc_html(__('End Date','majestic-support')))), MJTC_ALLOWED_TAGS);
			    	echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS);
				?>
			    <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('go', esc_html(__('Search', 'majestic-support')), array('class' => 'button mjtc-form-search')), MJTC_ALLOWED_TAGS); ?>
				<?php echo wp_kses(MJTC_formfield::MJTC_button('reset', esc_html(__('Reset', 'majestic-support')), array('class' => 'button mjtc-form-reset', 'onclick' => 'resetFrom();')), MJTC_ALLOWED_TAGS); ?>
			</form>
			<div class="mjtc-admin-report">
				<div class="mjtc-admin-subtitle"><?php echo esc_html(__('Agent Statistics','majestic-support')); ?></div>
				<div class="mjtc-admin-rep-graph" id="curve_chart" style="height:400px;width:98%; "></div>
			</div>
			<div class="mjtc-admin-report">
				<div class="mjtc-admin-staff-list p0">
					<?php
						if(!empty($MJTC_agent)){ ?>
						<div class="mjtc-admin-staff-wrapper">
							<div class="mjtc-admin-staff-cnt">
								<div class="mjtc-report-staff-image">
									<?php
									echo wp_kses_post(MJTC_get_avatar($MJTC_agent->uid, 'mjtc-report-staff-pic')); ?>
								</div>
								<div class="mjtc-report-staff-cnt">
									<div class="mjtc-report-staff-info mjtc-report-staff-name">
										<?php
											if($MJTC_agent->firstname && $MJTC_agent->lastname){
												$MJTC_agentname = $MJTC_agent->firstname . ' ' . $MJTC_agent->lastname;
											}else{
												$MJTC_agentname = $MJTC_agent->display_name;
											}
											echo esc_html($MJTC_agentname);
										?>
									</div>
									<div class="mjtc-report-staff-info mjtc-report-staff-email">
										<?php
											if($MJTC_agent->display_name){
												$MJTC_username = $MJTC_agent->display_name;
											}else{
												$MJTC_username = $MJTC_agent->user_nicename;
											}
											echo esc_html($MJTC_username);
										?>
									</div>
									<div class="mjtc-report-staff-info mjtc-report-staff-email">
										<?php
											if($MJTC_agent->email){
												$MJTC_email = $MJTC_agent->email;
											}else{
												$MJTC_email = $MJTC_agent->user_email;
											}
											echo esc_html($MJTC_email);
										?>
									</div>
								</div>
							</div>
							<div class="mjtc-admin-staff-boxes">
								<?php
								$MJTC_rating_class = 'box6';
									if(in_array('feedback', majesticsupport::$_active_addons)){
										if($MJTC_agent->avragerating > 4){
											$MJTC_rating_class = 'box65';
										}elseif($MJTC_agent->avragerating > 3){
											$MJTC_rating_class = 'box64';
										}elseif($MJTC_agent->avragerating > 2){
											$MJTC_rating_class = 'box63';
										}elseif($MJTC_agent->avragerating > 1){
											$MJTC_rating_class = 'box62';
										}elseif($MJTC_agent->avragerating > 0){
											$MJTC_rating_class = 'box61';
										}
									}
									if ( in_array( 'timetracking', majesticsupport::$_active_addons, true ) ) {

									    $time = isset( $MJTC_agent->time[0] ) ? (int) $MJTC_agent->time[0] : 0;

									    $MJTC_hours = (int) floor( $time / 3600 );
									    $MJTC_mins  = (int) floor( $time / 60 );
									    $MJTC_mins  = (int) floor( $MJTC_mins % 60 );
									    $MJTC_secs  = (int) floor( $time % 60 );

									    $MJTC_avgtime = esc_html( sprintf( '%02d:%02d:%02d', $MJTC_hours, $MJTC_mins, $MJTC_secs ) );
									}
						        ?>
								<?php
									$MJTC_open_percentage = 0;
									$MJTC_close_percentage = 0;
									$MJTC_overdue_percentage = 0;
									$MJTC_answered_percentage = 0;
									$MJTC_pending_percentage = 0;
									if(isset($MJTC_agent) && isset($MJTC_agent->allticket) && $MJTC_agent->allticket != 0){
									    $MJTC_open_percentage = round(($MJTC_agent->openticket / $MJTC_agent->allticket) * 100);
									    $MJTC_close_percentage = round(($MJTC_agent->closeticket / $MJTC_agent->allticket) * 100);
									    $MJTC_overdue_percentage = round(($MJTC_agent->overdueticket / $MJTC_agent->allticket) * 100);
									    $MJTC_answered_percentage = round(($MJTC_agent->answeredticket / $MJTC_agent->allticket) * 100);
									    $MJTC_pending_percentage = round(($MJTC_agent->pendingticket / $MJTC_agent->allticket) * 100);
									}
									if(isset($MJTC_agent) && isset($MJTC_agent->allticket) && $MJTC_agent->allticket != 0){
									    $MJTC_allticket_percentage = 100;
									}
								?>
								<div class="mjtc-support-count">
								    <div class="mjtc-support-link">
								        <a class="mjtc-support-link mjtc-support-green" href="#" data-tab-number="1" title="<?php echo esc_attr(__('Open Ticket', 'majestic-support')); ?>">
								            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($MJTC_open_percentage); ?>" data-tab-number="1"></div>
								            <div class="mjtc-support-link-text mjtc-support-green">
								            	<div class="ms-mini-val">
								            		<?php echo esc_html($MJTC_agent->openticket); ?>
								            	</div>
								            	<div class="ms-mini-label">
									                <?php echo esc_html(__('Open', 'majestic-support'));
									                ?>
								            	</div>
								            </div>
								        </a>
								    </div>
								    <div class="mjtc-support-link">
								        <a class="mjtc-support-link mjtc-support-brown" href="#" data-tab-number="2" title="<?php echo esc_attr(__('Answered Tickets', 'majestic-support')); ?>">
								            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($MJTC_answered_percentage); ?>" ></div>
								            <div class="mjtc-support-link-text mjtc-support-brown">
								            	<div class="ms-mini-val">
								            		<?php echo esc_html($MJTC_agent->answeredticket); ?>
								            	</div>
								            	<div class="ms-mini-label">
									                <?php echo esc_html(__('Answered', 'majestic-support'));
									                ?>
								            	</div>
								            </div>
								        </a>
								    </div>
								    <div class="mjtc-support-link">
					                    <a class="mjtc-support-link mjtc-support-yellow" href="#" data-tab-number="3" title="<?php echo esc_attr(__('pending tickets', 'majestic-support')); ?>">
					                        <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($MJTC_pending_percentage); ?>"></div>
					                        <div class="mjtc-support-link-text mjtc-support-yellow">
					                        	<div class="ms-mini-val">
								            		<?php echo esc_html($MJTC_agent->pendingticket); ?>
								            	</div>
								            	<div class="ms-mini-label">
									                <?php echo esc_html(__('Pending', 'majestic-support'));
									                ?>
								            	</div>
					                        </div>
					                    </a>
					                </div>
					                <?php if(in_array('overdue', majesticsupport::$_active_addons)){ ?>
									    <div class="mjtc-support-link">
									        <a class="mjtc-support-link mjtc-support-orange" href="#" data-tab-number="4" title="<?php echo esc_attr(__('Overdue Tickets', 'majestic-support')); ?>">
									            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($MJTC_overdue_percentage); ?>" ></div>
									            <div class="mjtc-support-link-text mjtc-support-orange">
									            	<div class="ms-mini-val">
									            		<?php echo esc_html($MJTC_agent->overdueticket); ?>
									            	</div>
									            	<div class="ms-mini-label">
										                <?php echo esc_html(__('Overdue', 'majestic-support'));
										                ?>
									            	</div>
									            </div>
									        </a>
									    </div>
									<?php } ?>
								    <div class="mjtc-support-link">
								        <a class="mjtc-support-link mjtc-support-red" href="#" data-tab-number="5" title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>">
								            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($MJTC_close_percentage); ?>" ></div>
								            <div class="mjtc-support-link-text mjtc-support-red">
								            	<div class="ms-mini-val">
								            		<?php echo esc_html($MJTC_agent->closeticket); ?>
								            	</div>
								            	<div class="ms-mini-label">
									                <?php echo esc_html(__('Closed', 'majestic-support'));
									                ?>
								            	</div>
								            </div>
								        </a>
								    </div>
									<?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
										<div class="mjtc-support-link <?php echo esc_attr($MJTC_rating_class)?>">
											<a href="#" class="mjtc-support-link mjtc-support-mariner" title="<?php echo esc_attr(__('Average Rating', 'majestic-support')); ?>">
												<span class="mjtc-report-box-number">
													<?php if($MJTC_agent->avragerating > 0){ ?>
														<span class="rating" ><?php echo esc_html(round($MJTC_agent->avragerating,1)); ?></span>/5
													<?php }else{ ?>
														NA
													<?php } ?>
												</span>
												<span class="mjtc-report-box-title"><?php echo esc_html(__('Average Rating','majestic-support')); ?></span>
												<div class="mjtc-report-box-color"></div>
											</a>
										</div>
									<?php } ?>
									<?php if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>
										<div class="mjtc-support-link">
											<a href="#" class="mjtc-support-link mjtc-support-purple" title="<?php echo esc_attr(__('Average Time', 'majestic-support')); ?>">
												<span class="mjtc-report-box-number">
													<span class="time" >
														<?php echo esc_html($MJTC_avgtime); ?>
													</span>
													<span class="exclamation" >
														<?php
														if($MJTC_agent->time[1] != 0){
											            	echo esc_html('!');
											            }
														?>
													</span>
												</span>
												<span class="mjtc-report-box-title"><?php echo esc_html(__('Average Time','majestic-support')); ?></span>
												<div class="mjtc-report-box-color"></div>
											</a>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php
					} ?>
				</div>
			</div>
			<div class="mjtc-admin-report">
				<div class="mjtc-admin-subtitle">
					<?php echo esc_html(__('Tickets','majestic-support')); ?>
				</div>
				<?php
				if(!empty(majesticsupport::$_data['staff_tickets'])){ ?>
					<table id="majestic-support-table" class="mjtc-admin-report-tickets">
						<tr class="majestic-support-table-heading">
							<th class="left majestic-support-table-title"><?php echo esc_html(__('Subject','majestic-support')); ?></th>
							<th class="majestic-support-table-ticketstatus"><?php echo esc_html(__('Status','majestic-support')); ?></th>
							<th class="majestic-support-table-ticketstatus"><?php echo esc_html(__('Priority','majestic-support')); ?></th>
							<th class="majestic-support-table-created-date"><?php echo esc_html(__('Created','majestic-support')); ?></th>
							<?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
								<th class="majestic-support-table-rating"><?php echo esc_html(__('Rating','majestic-support')); ?></th>
							<?php }?>
							<?php if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>
								<th class="majestic-support-table-time-taken"><?php echo esc_html(__('Time Taken','majestic-support')); ?></th>
							<?php }?>
						</tr>
						<?php
						foreach(majesticsupport::$_data['staff_tickets'] AS $MJTC_ticket) {
							if(in_array('timetracking', majesticsupport::$_active_addons)){
								$MJTC_hours = floor($MJTC_ticket->time / 3600);
					            $MJTC_mins = floor($MJTC_ticket->time / 60);
					            $MJTC_mins = floor($MJTC_mins % 60);
					            $MJTC_secs = floor($MJTC_ticket->time % 60);
					            $MJTC_avgtime = esc_html(sprintf('%02d:%02d:%02d', $MJTC_hours, $MJTC_mins, $MJTC_secs));
					        }
							if(in_array('feedback', majesticsupport::$_active_addons)){
					            $MJTC_rating_color = 0;
					            if($MJTC_ticket->rating > 4){
					            	$MJTC_rating_color = '#ea1d22';
					            }elseif($MJTC_ticket->rating > 3){
					            	$MJTC_rating_color = '#f58634';
					            }elseif($MJTC_ticket->rating > 2){
					            	$MJTC_rating_color = '#a8518a';
					            }elseif($MJTC_ticket->rating > 1){
					            	$MJTC_rating_color = '#0098da';
					            }elseif($MJTC_ticket->rating > 0){
					            	$MJTC_rating_color = '#069a2e';
					            }
					        } ?>
							<tr>
								<td class="overflow left majestic-support-table-title">
									<span class="majestic-support-table-responsive-heading">
										<?php echo esc_html(__('Subject','majestic-support')); ?> :
									</span>
									<a title="<?php echo esc_attr(__('Ticket', 'majestic-support')); ?>" target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid='.esc_attr($MJTC_ticket->id))); ?>"><?php echo esc_html($MJTC_ticket->subject); ?></a>
								<?php
								if($MJTC_agent->id != $MJTC_ticket->staffid){
									$MJTC_show_flag = 1;
									?>
									<font style="color:#1C6288;font-size:20px;margin:0px 5px;">*</font>
								<?php } ?>
								</td>
								<td class="majestic-support-table-ticketstatus">
									<span class="majestic-support-table-responsive-heading">
										<?php echo esc_html(__('Status','majestic-support')); ?> :
									</span>
									<?php
							            // 1 -> New Ticket
							            // 2 -> Waiting admin/staff reply
							            // 3 -> in progress
							            // 4 -> waiting for customer reply
							            // 5 -> close ticket
										$MJTC_status = '';
										/*switch($MJTC_ticket->status){
											case 0:
												$MJTC_status = '<font color="#159667">'. esc_html(__('New','majestic-support')).'</font>';
												if($MJTC_ticket->isoverdue == 1)
													$MJTC_status = '<font color="#B82B2B">'. esc_html(__('Overdue','majestic-support')).'</font>';
											break;
											case 1:
												$MJTC_status = '<font color="#f39f10">'. esc_html(__('Pending','majestic-support')).'</font>';
												if($MJTC_ticket->isoverdue == 1)
													$MJTC_status = '<font color="#B82B2B">'. esc_html(__('Overdue','majestic-support')).'</font>';
											break;
											case 2:
												$MJTC_status = '<font color="#f39f10">'. esc_html(__('In Progress','majestic-support')).'</font>';
												if($MJTC_ticket->isoverdue == 1)
													$MJTC_status = '<font color="#B82B2B">'. esc_html(__('Overdue','majestic-support')).'</font>';
											break;
											case 3:
												$MJTC_status = '<font color="#2168A2">'. esc_html(__('Answered','majestic-support')).'</font>';
												if($MJTC_ticket->isoverdue == 1)
													$MJTC_status = '<font color="#B82B2B">'. esc_html(__('Overdue','majestic-support')).'</font>';
											break;
											case 4:
												$MJTC_status = '<font color="#3D355A">'. esc_html(__('Closed','majestic-support')).'</font>';
											break;
											case 5:
												$MJTC_status = '<font color="#3D355A">'. esc_html(__('Merged and closed','majestic-support')).'</font>';
											break;
										}*/
										if (!in_array($MJTC_ticket->status, [5, 6]) && $MJTC_ticket->isoverdue == 1) {
											$MJTC_status = __('Overdue','majestic-support');
							                $MJTC_color = '#FFFFFF';
							                $MJTC_bgcolor = '#DB624C';
						                } else {
						                	$MJTC_status = $MJTC_ticket->statustitle;
							                $MJTC_color = $MJTC_ticket->statuscolour;
							                $MJTC_bgcolor = $MJTC_ticket->statusbgcolour;
						                }
									?>
                                    <span class="priority" style="background:<?php echo esc_attr($MJTC_bgcolor); ?>;color:<?php echo esc_attr($MJTC_color); ?>">
										<?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_status)); ?>
									</span>
								</td>
								<td class="majestic-support-table-ticketstatus">
									<span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Priority','majestic-support')); ?> :</span>
									<span style="background-color:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;" class="mjtc-sprt-rep-prty"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?></span>
								</td>
								<td class="majestic-support-table-created-date">
									<span class="majestic-support-table-responsive-heading"><?php echo esc_html(__("Created",'majestic-support'));?>: </span>
									<?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'],MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>
								</td>
								<?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
									<td class="majestic-support-table-rating">
										<span class="majestic-support-table-responsive-heading"> <?php echo esc_html(__('Rating','majestic-support')); ?> : </span>
										<?php if($MJTC_ticket->rating > 0){ ?>
											<span style="color:<?php echo esc_attr($MJTC_rating_color); ?>;font-weight:bold;font-size:16px;" > <?php echo esc_html($MJTC_ticket->rating);?></span>
											<?php echo esc_html(__('Out of','majestic-support')).'<span style="font-weight:bold;font-size:15px;" >&nbsp;5</span>';
										}else{
											echo esc_html('NA');
										} ?>
									</td>
								<?php } ?>
								<?php if(in_array('timetracking', majesticsupport::$_active_addons)){ ?>
									<td class="majestic-support-table-time-taken">
										<span class="majestic-support-table-responsive-heading"><?php echo esc_html(__('Time Taken','majestic-support')); ?> : </span>
										<?php echo esc_html($MJTC_avgtime); ?>
									</td>
								<?php } ?>
							</tr>
						<?php
						} ?>
					</table>
					<?php
				} else {
					MJTC_layout::MJTC_getNoRecordFound();
				} ?>
			</div>
			<?php
			if($MJTC_show_flag == 1){ ?>
				<div class="mjtc-form-button">
		        	<?php echo wp_kses('<font style="color:#1C6288;font-size:20px;margin:0px 5px;">*</font>'.esc_html(__('Tickets not assigned to the agent','majestic-support')), MJTC_ALLOWED_TAGS); ?>
		        	<a href="?page=majesticsupport_reports&mjslay=staffreport" class="mjtc-form-cancel"><?php echo esc_html(__('Cancel','majestic-support')); ?></a>
		        </div>
	        	<?php
        	}
	        if(!empty(majesticsupport::$_data['staff_tickets'])){
			    if (majesticsupport::$_data[1]) {
			        $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
			        echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
			    }
			}
			?>
			</div>
		</div>
	</div>
