<?php
/**
 * Plugin Name: Theme My Ontraport Smartform
 * Plugin URI: http://www.itmooti.com/
 * Description: Custom Themes for Ontraport/Office Auto Pilot Smart Forms
 * Version: 1.0.2
 * Author: ITMOOTI
 * Author URI: http://www.itmooti.com/
 */
 

class itmooti_oap_custom_theme
{
    private $options;
	private $url="http://app.itmooti.com/wp-plugins/oap-utm/api.php";
	
	public function __construct(){
		add_action( 'admin_menu', array( $this, 'add_itmooti_oap_custom_theme' ) );
		add_action( 'admin_notices', array( $this, 'show_license_info' ) );
		add_shortcode( 'custom_form_style', array($this, 'itmooti_oap_custom_theme'));
    }
	
	public function itmooti_oap_custom_theme( $atts ) {
		$license_key=get_option('oap_custom_theme_license_key', "");
		if(!empty($license_key)){
			$request= "verify";
			$postargs = "plugin=itmooti-oap-themes&domain=".urlencode($_SERVER['HTTP_HOST'])."&license_key=".urlencode($license_key)."&request=".urlencode($request);
			$session = curl_init($this->url);
			curl_setopt ($session, CURLOPT_POST, true);
			curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$response = json_decode(curl_exec($session));
			curl_close($session);
			if(isset($response->status) && $response->status=="success"){
				$atts = shortcode_atts( array(
					'theme' => 'Amber'
				), $atts );
				$atts["theme"]=strtolower($atts["theme"]);
				add_action( 'wp_enqueue_scripts', array($this, 'itmooti_oap_custom_js'));
				wp_enqueue_script('jquery');
				wp_enqueue_script('oap_custom_theme-'.$atts["theme"], plugins_url('themes/'.$atts["theme"].'/js.js', __FILE__));
				wp_enqueue_style('oap_custom_theme-'.$atts["theme"], plugins_url('themes/'.$atts["theme"].'/style.css', __FILE__));
			}
		}
		return "<!-- Wrong API Credentials -->";
	}

	public function show_license_info(){
		$license_key=get_option('oap_custom_theme_license_key', "");
		if(empty($license_key)){
			echo '<div class="updated">
        		<p>Ontraport/Office Auto Pilot Custom Themes: How do I get License Key?<br />Please visit this URL <a href="http://app.itmooti.com/wp-plugins/oap-utm/license/">http://app.itmooti.com/wp-plugins/oap-utm/license/</a> to get a License Key .</p>
	    	</div>';
		}
		$message=get_option("itmooti-oap-themes_message", "");
		if($message!=""){
			echo '<div class="error">
        		<p>Ontraport/Office Auto Pilot Custom Themes: '.$message.'</p>
	    	</div>';
		}
	}
	
	public function add_itmooti_oap_custom_theme(){
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'OAP Custom Theme Settings', 
            'manage_options', 
            'itmooti-oap-admin', 
            array( $this, 'create_admin_page' )
        );
    }
	
	 public function create_admin_page(){
        if(isset($_POST["oap_custom_theme_license_key"]))
			add_option("oap_custom_theme_license_key", $_POST["oap_custom_theme_license_key"]) or update_option("oap_custom_theme_license_key", $_POST["oap_custom_theme_license_key"]);
		?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Ontraport/Office Auto Pilot Custom Themes Settings</h2>           
            <form method="post">
           	  	<h3>Plugin Credentials</h3>
                Provide Plugin Credentials below:
                <?php $license_key=get_option('oap_custom_theme_license_key', "");?>
                <table class="form-table">
                	<tr>
                    	<th scope="row">License Key</th>
                        <td><input type="text" name="oap_custom_theme_license_key" id="oap_custom_theme_license_key" value="<?php echo $license_key?>" /></td>
                   	</tr>
              	</table>
				<?php				
				if(!empty($license_key)){
					$request= "verify";
					$postargs = "plugin=itmooti-oap-themes&domain=".urlencode($_SERVER['HTTP_HOST'])."&license_key=".urlencode($license_key)."&request=".urlencode($request);
					$session = curl_init($this->url);
					curl_setopt ($session, CURLOPT_POST, true);
					curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
					curl_setopt($session, CURLOPT_HEADER, false);
					curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
					$response = json_decode(curl_exec($session));
					curl_close($session);
					if(isset($response->status) && $response->status=="success"){
						if(isset($response->message))
							add_option("itmooti-oap-themes_message", $response->message) or update_option("itmooti-oap-themes_message", $response->message);
						echo "Successfully authenticated. You can use the Custom themes now.";
					}
					else{
						if(isset($response->message))
							echo $response->message;
						else
							echo "Error in license key verification. Try again later";
					}
				}
				?>
                <h3>How to Use</h3>
                <p>Use shortcode <strong>[custom_form_style]</strong> in post or page content to include the Theme files.</p>
                <?php
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }
}
$itmooti_oap_custom_theme=new itmooti_oap_custom_theme();
?>