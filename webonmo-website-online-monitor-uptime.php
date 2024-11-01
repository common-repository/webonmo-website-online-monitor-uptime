<?php
/**
 *WebOnMo - plugin for website uptime monitoring.
 *@author WebOnMo
 *@license GPL-2.0+
 *@link https://webonmo.com/wordpress
 *
 *@wordpress-plugin   
 *Plugin Name: WebOnMo - Website Online Monitor Uptime
 *Plugin URI: https://webonmo.com/wordpress
 *Description: Very basic and simple plugin to monitor uptime on websites. Uptime monitoring is done from external servers and displayed in WordPress admin. Users get an email when the website is down.
 *Version: 1.0.0
 *Author: WebOnMo
 *Author URI: https://webonmo.com
 *Text Domain: webonmo-website-online-monitor-uptime
 *License: GPL-2.0+
 *License URI: http://www.gnu.org/licenses/gpl.html
 */
 


function webonmo_add_menu() {
	add_submenu_page ( "options-general.php", "WebOnMo Plugin", "WebOnMo Plugin", "manage_options", "webonmo-uptime-monitor", "webonmo_uptime_monitor_page" );
}
add_action ( "admin_menu", "webonmo_add_menu" );
 
/**
 * Setting Page Options
 */
function webonmo_uptime_monitor_page() {
	?>
<div class="wrap">
	<!-- <h1></h1> -->
	<form method="post" action="options.php">
            <?php
	settings_fields ( "webonmo_uptime_monitor_config" );
	do_settings_sections ( "webonmo-uptime-monitor" );
	submit_button ();
	?>
	</form>
</div>
 
<?php
}
 
/**
 * Init setting section
 */
function webonmo_uptime_monitor_settings() {
	add_settings_section ( "webonmo_uptime_monitor_config", "", null, "webonmo-uptime-monitor" );
	add_settings_field ( "webonmo-uptime-monitor-text", "Enter your details to connect your website to WebOnMo.", "webonmo_uptime_monitor_options", "webonmo-uptime-monitor", "webonmo_uptime_monitor_config", array( 'class' => 'webonmo-uptime-monitor-text' ) );
	register_setting ( "webonmo_uptime_monitor_config", "webonmo-uptime-monitor-text" );
	register_setting ( "webonmo_uptime_monitor_config", "webonmo-uptime-monitor-text-b" );
	register_setting ( "webonmo_uptime_monitor_config", "webonmo-uptime-monitor-text-c" );
}
add_action ( "admin_init", "webonmo_uptime_monitor_settings" );
 
/**
 * Add textfield value to setting page
 */
function webonmo_uptime_monitor_options() {
	?>
	<style>
	
	.webonmo-uptime-monitor-text {
			background: #FFF;
			border:solid 1px #CCC;
			
	}
	
	.webonmo-uptime-monitor-text th {
			padding-left:1em;
			
	}
	
	
	
	</style>
<div class="postbox" style="border:none; width: 100%; padding: 0 0 0 1em;">
<?php
$alreadySavedC = stripslashes_deep ( esc_attr ( get_option ( 'webonmo-uptime-monitor-text-c' ) ) );

if( strlen($alreadySavedC) > 2) { 

	//Get the status from external monitoring system
	$response = wp_remote_get( 'https://webonmo.com/get.wp.php?two='.$alreadySavedC );
	 
	if ( is_array( $response ) && ! is_wp_error( $response ) ) {
		$headers = $response['headers']; // array of http header lines
		$body    = $response['body']; // use the content
	}

	if(!is_array( $response )) {
		echo '<p style="color:#FF0000">An error occured. Please try again later (or contact us if the problem persists, https://webonmo.com).</p>';
	} else {
		$body = str_replace("WPPATH/", plugin_dir_url( __FILE__ ), $body);
		echo $body;
	}
	
	
	?>


<?php } ?>
<p style="margin-top:1em;margin-bottom:0.4em">1. Enter your domain name WITHOUT http:// (or https://). Example: <strong>yourdomainis.com</strong> or <strong>www.yourdomainis.com</strong>. Please use www if your website is using www - do NOT use www if your website does not use it:</p>



	<input type="text" id="flda" placeholder="Domain name" style="margin-top:0.2em;" name="webonmo-uptime-monitor-text"
		value="<?php
		$siteEmail = get_option('admin_email');
		$siteUrlIs = webonmo_com_get_site_url_here();
		$siteUrlIs = str_replace("http://", "", $siteUrlIs);
		$siteUrlIs = str_replace("https://", "", $siteUrlIs);
		$siteUrlIs = trim($siteUrlIs, "/");
		
	$alreadySaved = stripslashes_deep ( esc_attr ( get_option ( 'webonmo-uptime-monitor-text' ) ) );
	
	if(strlen(@$alreadySaved) > 0) {
		echo $alreadySaved;
	} 
	?>" />
	<input type="button" class="button button-primary" style="background:#333;margin-top:0.2em;" value="Use: <?php echo $siteUrlIs; ?>" onclick="document.getElementById('flda').value = '<?php echo $siteUrlIs; ?>';">
	<br><br>
	
	<p style="margin-bottom:0.4em">2. Enter your email (you will receive notifications when your site is down to this email):</p>
	<input type="text" id="fldb" placeholder="E-mail" style="margin-top:0.2em;" name="webonmo-uptime-monitor-text-b"
		value="<?php
		
	$alreadySavedB = stripslashes_deep ( esc_attr ( get_option ( 'webonmo-uptime-monitor-text-b' ) ) );
	
	if(strlen(@$alreadySavedB) > 0) {
		echo $alreadySavedB;
	} 
	?>" />
	<input type="button" class="button button-primary" style="background:#333;margin-top:0.2em;" value="Use: <?php echo $siteEmail; ?>" onclick="document.getElementById('fldb').value = '<?php echo $siteEmail; ?>';">
	
	<br><br>
	<?php 
	if( strlen($alreadySaved) > 2&& strlen($alreadySavedB) > 2) { ?>
	
	<p style="margin-bottom:0.4em">3. Now create/connect an account here (link to WebOnMo):</p>
	<a href="https://webonmo.com/?wpd=<?php echo @$alreadySaved; ?>&wpe=<?php echo @$alreadySavedB; ?>#start" target="_blank"><div class="button button-primary" style="background:#333;">Connect</div></a>
	<br><br>
	<p style="margin-bottom:0.4em">4. Then enter the code received from WebOnMo when registering (in step 3 above). The code you should enter is this one that you see in your browser adress field when registered on WebOnMo.</p>
	<img src="<?php echo plugin_dir_url( __FILE__ ) . 'code.gif'; ?>"><br>
	<input type="text" id="fldc" placeholder="Enter code" name="webonmo-uptime-monitor-text-c"
		value="<?php
		
	
	if(strlen(@$alreadySavedC) > 0) {
		echo $alreadySavedC;
	} 
	?>" />
	<p style="font-size:0.9em">(NOTE: If you request a new login link from WebOnMo you get a new code and must enter the new code here.)</p>
	
	
	<?php } ?>
</div>
<?php
}
 
/**
 * Append saved textfield value to each post
 */
add_filter ( 'the_content', 'webonmo_com_content' );
function webonmo_com_content($content) {
	return $content . stripslashes_deep ( esc_attr ( get_option ( 'webonmo-uptime-monitor-text' ) ) );
}



/**
* Show on dashboard
*/
function webonmo_com_dashboard_test() {

	$alreadySavedC = stripslashes_deep ( esc_attr ( get_option ( 'webonmo-uptime-monitor-text-c' ) ) );
	

	if(strlen($alreadySavedC) > 2) {
		
		//Get the status from external monitoring system
		$response = wp_remote_get( 'https://webonmo.com/get.wp.php?two='.$alreadySavedC );
		 
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$headers = $response['headers']; // array of http header lines
			$body    = $response['body']; // use the content
		}

		if(!is_array( $response )) {
			echo '<p style="color:#FF0000">An error occured. Please try again later (or contact us if the problem persists, https://webonmo.com).</p>';
		} else {
			$body = str_replace("WPPATH/", plugin_dir_url( __FILE__ ), $body);
			echo $body;
		}
	
	} else {
		echo "Connect (’Settings -> WebOnMo Plugin’) your website to WebOnMo to see uptime status here.";	
	}
	
}
function webonmo_com_dashboard_setup() {
    wp_add_dashboard_widget( 'webonmo_com_dashboard_test', __( 'WebOnMo (uptime status)' ),'webonmo_com_dashboard_test');
}
add_action('wp_dashboard_setup', 'webonmo_com_dashboard_setup');






/* Get site URL */
function webonmo_com_get_site_url_here( $blog_id = null, $path = '', $scheme = null ) {
    if ( empty( $blog_id ) || ! is_multisite() ) {
        $url = get_option( 'siteurl' );
    } else {
        switch_to_blog( $blog_id );
        $url = get_option( 'siteurl' );
        restore_current_blog();
    }
 
    $url = set_url_scheme( $url, $scheme );
 
    if ( $path && is_string( $path ) ) {
        $url .= '/' . ltrim( $path, '/' );
    }
 
    /**
     * Filters the site URL.
     *$url     The complete site URL including scheme and path.
     *$path    Path relative to the site URL. Blank string if no path is specified.
     *$scheme  Scheme to give the site URL context. Accepts 'http', 'https', 'login', 'login_post', 'admin', 'relative' or null.
     *$blog_id Site ID, or null for the current site.
     */
    return apply_filters( 'site_url', $url, $path, $scheme, $blog_id );
}