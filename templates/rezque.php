<?php
/**
 * Appointment Shortcode Render
 *
 * @author 		Boopis Media
 * @package 	Rezque Appointments
 * @version     0.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wp_register_style( 'datepicker', REZQUE_URL . '/assets/css/jquery.mobile.datepicker.css', array(), '1.0.0', 'all' );
wp_register_style( 'datepicker-theme', REZQUE_URL . '/assets/css/jquery.mobile.datepicker.theme.css', array(), '1.0.0', 'all' );
wp_register_style( 'rezque-custom', REZQUE_URL . '/assets/css/custom.css', array(), '1.0.0', 'all' );
wp_enqueue_style( 'datepicker' );
wp_enqueue_style( 'datepicker-theme' );
wp_enqueue_style( 'rezque-custom' );

wp_register_script( 'jquery-datepicker', REZQUE_URL . '/assets/js/datepicker.js' );

wp_enqueue_script('jquery');
wp_enqueue_script( 'jquery-datepicker' );     

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";

if (isset($_COOKIE['__utmz'])) {
	list($domain_hash, $timestamp, $session_number, $campaign_numer, $campaign_data) = split('[\.]', $_COOKIE["__utmz"],5);
	parse_str(strtr($campaign_data, "|", "&"));
	$source = isset($utmcsr) ? $utmcsr : "";
	$medium = isset($utmcmd) ? $utmcmd : "";
	$campaign = isset($utmccn) ? $utmccn : "";
	$content = isset($utmcct) ? $utmcct : "";
} else {
	$params = $_SERVER['QUERY_STRING'];
	parse_str($_SERVER['QUERY_STRING']);
	$source = isset($utm_source) ? $utm_source : "";
	$medium = isset($utm_medium) ? $utm_medium : "";
	$campaign = isset($utm_campaign) ? $utm_campaign : "";
	$content = isset($utm_content) ? $utm_content : "";
}

?>	

<!-- BEGIN row -->
<div class="row">

	<form id="rezque">
		<?php wp_nonce_field( 'rezque_form' ); ?>
		<!-- fieldsets -->
		<fieldset>
			<h2 class="fs-title">Select a Date</h2>
			<div id="date"></div>
		</fieldset>
		<fieldset>
			<h2 class="fs-title">Select a Time</h2>
			<div class="date-slots"></div>
			<input type="button" name="previous" class="previous action-button" value="Previous" />
		</fieldset>
		<fieldset>
			<h2 class="fs-title">Finish Booking!</h2>
			<h3 class="fs-subtitle"></h3>
			<input type="text" placeholder="Name" id="name" required>
			<input type="email" placeholder="Email Address" id="email" required>
			<input type="tel" placeholder="Phone Number" id="phone" required>
			<div id="location">
				<div id="hof">
					<span>Location:</span>
					<div class="btn-group btn-group-vertical" style="width:100%" data-toggle="buttons">
						<label for="appointment_hof_home_visit" class="btn btn-default">
							<input type="radio" name="hof" id="appointment_hof_home_visit" value="home_visit" required><?php echo $home_label; ?>
						</label>

						<label for="appointment_hof_office_visit" class="btn btn-default">
							<input type="radio" name="hof" id="appointment_hof_office_visit" value="office_visit"><?php echo $office_label; ?>
						</label>
						<p class="help-block text-danger"></p>
					</div>
				</div>
				<input type="text" class="form-control" placeholder="Address" id="address" required style="display:none;">
			</div>
			<input type="button" name="previous" class="previous action-button" value="Previous" />
			
			<input type="hidden" id="day">
			<input type="hidden" id="start">
			<input type="hidden" id="end">
			<input type="hidden" id="calendar" value="<?php echo $calendar_id; ?>">
			<input type="hidden" id="date_format">
			<input type="hidden" id="ip" value="<?php echo $ip; ?>">
			<input type="hidden" id="referer" value="<?php echo $referer; ?>">
			<input type="hidden" id="source" value="<?php echo $source; ?>">
			<input type="hidden" id="medium" value="<?php echo $medium; ?>">
			<input type="hidden" id="campaign" value="<?php echo $campaign; ?>">
			<input type="hidden" id="content" value="<?php echo $content; ?>">

			<input type="submit" name="submit" class="submit submit-button" value="Book Appointment" />
		</fieldset>
	</form>

<!-- END row -->
</div>

<?php
wp_register_script( 'rezque-custom', REZQUE_URL . '/assets/js/custom.js' );
wp_enqueue_script( 'rezque-custom' ); 

$redirect = array( 'redirect_url' => get_option('rezque_success_url') );

wp_localize_script( 'rezque-custom', 'rezque_success', $redirect ); 
?>

<div class="spinner-outer-wrapper" style="display:none;position:fixed;top:0;left:0;z-index: 1100;width:100%;height:100%;background:#000;opacity:0.5;filter:alpha(opacity=50);">
	<div class="spinner-inner-wrapper" style="position:fixed;left:50%;top:40%;z-index:1101;">
		<div id="ajaxloader"></div>
	</div>
</div>
