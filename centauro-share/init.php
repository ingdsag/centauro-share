<?php
/*	Plugin Name: Centauro Share
	Plugin URI: http://www.centaurosms.com.ve
	Description: Compartir en facebook, en twitter, google plus y en centauro con este plugin.
	Version: 1.0.0
	Author: Daniel Arias
	Author URI: http://www.codehater.com
	License: GPLv2 or later
	Text Domain: social-share
	*/
	Global $notSendingMessage;
	if (!isset($notSendingMessage))
	{
		$notSendingMessage = true;
	}

	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	date_default_timezone_set ("America/Caracas");
	include_once("centaurosms.php");
	$SMS = new CentauroSMS('625810897605994', 'fIgHBEoTqGRPWyAtlLfU');
	function centauro_scripts()
	{
		wp_enqueue_script('jquery');
		wp_register_script('myjs',plugins_url("centauro-scripts.js", __FILE__ ),"jquery");
		wp_enqueue_script('myjs');
		wp_enqueue_style("centauro-style", plugins_url( 'centauro-style.css', __FILE__ ));
	}
	add_action( 'admin_print_scripts', 'centauro_scripts' );
	add_action( 'wp_enqueue_scripts', 'centauro_scripts' );
    add_action('get_footer',"run_plugin");

	function share_centauro_menu()
	{
		add_menu_page("Share Centauro","Share Centauro","manage_options","share-centauro-settings","share_centauro_settings","div");
	}
	function share_centauro_settings()
	{
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( ( 'You do not have sufficient permissions to access this page.' ) );
		}
		require_once("centauro-admin.php");
	}
	add_action( 'admin_menu', 'share_centauro_menu' );

    function run_plugin()
    {
        Global $notSendingMessage;
        $ubicar_derecha = get_option('ubicar-derecha');
        $derecha_class = "";
        if($ubicar_derecha=='checked')
        {
            $derecha_class = " right";
        }
?>


<div class='centauro-share-bar<?php echo $derecha_class;?>'>
	<?php
		if ( ! is_admin() && $notSendingMessage === true )
		{
			showBar();
		}
	?>
</div>
<?php
    }
    function showBar()
    {
        $fb_share = get_option('fb-share');
        $tw_share = get_option('tw-share');
        $gplus_share = get_option('gplus-share');
        $mail_share = get_option('mail-share');
        $sms_share = get_option('sms-share');

        $texto_twitter = get_option('texto-twitter');
        $titulo_correo = get_option('titulo-correo');
        $texto_correo = get_option('texto-correo');

        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $texto_correo = str_replace("%ENLACE%",$actual_link,$texto_correo);
        $twitter_link = json_decode(file_get_contents("http://api.bit.ly/v3/shorten?login=o_4sca96n38i&apiKey=R_587aba194e7b96f888cfbed2a3b1cb74&longUrl=".urlencode($actual_link)."&format=json"))->data->url;
        $texto_twitter = str_replace("%ENLACE%",$twitter_link,$texto_twitter);


        if($fb_share=='checked')
        {
            ?>

            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $actual_link;?>" class='popup-window'>
                <div class='centauro-share-facebook'></div>
            </a>
        <?php
        }
        if($tw_share=='checked')
        {
            ?>
            <a href="https://twitter.com/intent/tweet?
            related=centauroSMS
            &text=<?php echo $texto_twitter;?>"
               class='popup-window'>
                <div class='centauro-share-twitter'>
                </div>
            </a>
        <?php
        }
        if($gplus_share=='checked')
        {
            ?>
            <a href="https://plus.google.com/share?url=<?php echo $actual_link;?>" class='popup-window'>
                <div class='centauro-share-google'>
                </div>
            </a>
        <?php
        }
        if($mail_share=='checked')
        {
            ?>
            <a href="mailto:?subject=<?php echo $titulo_correo; ?>&body=<?php echo $texto_correo; ?>" title="Share by Email">
                <div class='centauro-share-email'>
                </div>
            </a>
        <?php
        }
        if($sms_share=='checked')
        {
            //SI EL USUARIO YA HA COMPARTIDO TODAS LAS VECES NO SE MUESTRA EL BOTON
            ?>
            <a href="" class='popup-window sms'>
                <div class='centauro-share-sms'>
                </div>
            </a>
            <script>
                jQuery(".popup-window.sms").attr("href","<?php echo plugins_url( 'smsshare.php', __FILE__ ); ?>?titulo="+document.title+"&link=<?php echo $actual_link;?>");
            </script>
        <?php
        }
    }
