<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="wrap">
<?php
if (isset($_POST["update_settings"])):
	check_admin_referer( 'update_rezque_settings' );
	?>
	<div id="message" class="updated">
	<?php
		$url = $_POST["booking_success"];
		// Remove all illegal characters from a url
		$url = filter_var($url, FILTER_SANITIZE_URL);

		$url_valid = !filter_var($url, FILTER_VALIDATE_URL) === false;

		if ($url_valid === false) {
			echo "$url is not a valid URL<br>";
		} else {
			update_option("rezque_success_url", $url);
			echo "Settings saved";
		}

	?> 
	</div>  
<?php endif; ?>

	<h2>Rezque Appointment Settings</h2>

	<h3 class="title">Notice</h3>
	<p>Thank you for trying out rezque. The application is currently in beta, so please visit <a href="https://www.rezque.com" target="_blank">rezque.com</a> and request and invitation.</p>
	<p>You can preview a demo <a href="https://demo.boopis.com/wp_mprfq/?page_id=2">here</a>.</p>
	<p>Currently all requests are sent to a server that is hosted by heroku. Depending on the amount of requests that are made, service may be interrupted.</p>
	<p>If you want more reliable uptime after taking the application for a spin, <a href="https://boopis.com/contact" target="_blank">reach out</a> to discuss how we can provide better uptime support.</p>

	<h3 class="title">Instructions</h3>
	<p>To add a calendar to a page, post, or widget, add the following shortcode with options:<br><br>
		<kbd>[rezque id=""]</kbd> To add the calendar, enter your calendar id.<br><br>
		If office and home options have been enabled on rezque, you can add labels to personalize the selections:<br><br>
		<kbd>[rezque id="" home_label=""]</kbd> To add a label to home option, add text.<br>
		<kbd>[rezque id="" office_label=""]</kbd> To add a label to home option, add text.<br>
	</p>

	<form method="POST" action="">  
		<?php wp_nonce_field( 'update_rezque_settings' ); ?>
		<table class="form-table">  
			<tr valign="top">  
				<th scope="row">  
					<label for="send_address">  
						Redirect URL
					</label>   
				</th>  
				<td>
					<input type="text" class="regular-text" name="booking_success" value="<?php echo get_option('rezque_success_url'); ?>">
					<p class="description" id="tagline-description">Upon successful booking of appointment, define the redirect url.</p>
				</td>  
			</tr>
		</table>
		<p>  
			<input type="hidden" name="update_settings" value="Y" />  
			<input type="submit" value="Save settings" class="button-primary"/>  
		</p>  
	</form>

</div>