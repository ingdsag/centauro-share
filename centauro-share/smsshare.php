<?php
	define('WP_USE_THEMES', true);
    $titulo = "";
    include_once("centaurosms.php");
    $SMS = new CentauroSMS('YOURID', 'YOURSECRETHERE');
    if (isset($_REQUEST['titulo']))
    {
        $titulo = $_REQUEST['titulo'];
    }
    $link = "";
    if (isset($_REQUEST['link']))
    {
        $link = $_REQUEST['link'];
    }


	/** Loads the WordPress Environment and Template */
	$notSendingMessage = false;
	require ('../../../wp-load.php');
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $msg = "";
    wp_enqueue_script('jquery');
	if (isset($_POST['action']) && $_POST['action']=="send-sms")
	{
		create_ip_tables_if_needed();
		clean_ip_if_not_today();
		share_sms();
	}
	function share_sms()
	{
        Global $msg;
		if (user_can_send()===true)
		{
			store_user();
            $msg = "Si";
		}
	}
	function send_message()
	{
        Global $SMS;
        $telefono = $_REQUEST['telefono'];
        $usuario = $_REQUEST['nombre'];
        $mensaje_usuario = get_option('texto-mensaje');
        $mensaje_usuario = str_replace ( "%NOMBRE%", $_REQUEST['nombre'],$mensaje_usuario);
        $enlace = $_REQUEST['link'];
        $enlace = json_decode(file_get_contents("http://api.bit.ly/v3/shorten?login=o_4sca96n38i&apiKey=R_587aba194e7b96f888cfbed2a3b1cb74&longUrl=".urlencode($enlace)."&format=json"))->data->url;
        $mensaje_usuario = str_replace ( "%ENLACE%", $enlace,$mensaje_usuario);

        $json = '{"id":"0","cel":"'.$telefono.'","nom":"'.$usuario.'"}';
        if ($mensaje_usuario!="")
        {
            $msge = $mensaje_usuario;
            $response = $SMS->set_sms_send($json,$msge);

        }
        //AQUI SE ENVIA EL MENSAJE CON EL PLUGIN DE CENTAURO
	}
	function store_user()
	{
		global $wpdb;
		$ip = $_SERVER['REMOTE_ADDR'];
		$table_name = $wpdb->prefix."centauro_usuarios_mensajes_ips";
		$sql = "INSERT INTO $table_name (ip,veces) VALUES ('$ip','1')";
        $wpdb->get_results( $sql );
        send_message();
	}
	function user_can_send()
	{
		global $wpdb, $msg;
		$ip = $_SERVER['REMOTE_ADDR'];
		$table_name = $wpdb->prefix."centauro_usuarios_mensajes_ips";

		$sql = "SELECT ip,veces from $table_name WHERE ip = '$ip'";
		$results = $wpdb->get_results( $sql );
        $veces = get_option('intentos-sms');
        if (!is_numeric($veces))
        {
            $veces = 0;
        }
        if (!isset($_REQUEST['telefono']) || !isset($_REQUEST['nombre']) || $_REQUEST['telefono'] == "" || $_REQUEST['nombre'] == "")
        {
            $msg = "Error";
            return false;
        }
        elseif (is_null($results) || sizeof($results)<$veces)
        {
            $msg = "";
            return true;
        }
        else
        {
            $msg = "No";
            return false;
        }

	}
	function clean_ip_if_not_today()
	{
		$time_stamp = get_option('fecha_limpieza_ips');
		if ($time_stamp=="")
		{
			$time_stamp = date ('Ymd');
			update_option( 'fecha_limpieza_ips', $time_stamp);
		}
		$today = date('Ymd');
		if ($today == date('Ymd', strtotime($time_stamp)))
		{
            //ESTO SIGNIFICA QUE HOY SE LIMPIARON LAS IPS, POR LO TANTO NO SE LIMPIAN MAS
		}
		else
		{
            //ESTO SIGNIFICA QUE HOY NO SE LIMPIARON LAS IPS, POR LO TANTO SE LIMPIAN
			clean_ip();
			update_option( 'fecha_limpieza_ips', $today);
		}

	}
	function clean_ip()
	{
        //LIMPIEZA DE IPS
        global $wpdb;
        $ip = $_SERVER['REMOTE_ADDR'];
        $table_name = $wpdb->prefix."centauro_usuarios_mensajes_ips";
		$sql = "TRUNCATE table $table_name";
        $results = $wpdb->get_results( $sql );
	}
	function create_ip_tables_if_needed()
	{
        //CREACION DE LA TABLA SI ES NECESARIO (NUEVA INSTALACION DEL PLUGIN)
        global $wpdb;
		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}
		$table_name = $wpdb->prefix."centauro_usuarios_mensajes_ips";

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  name tinytext NOT NULL,
		  veces varchar(2) NOT NULL,
		  ip varchar(20) DEFAULT '' NOT NULL,
		  UNIQUE KEY id (id)
		) $charset_collate;";
		dbDelta( $sql );
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<link rel="stylesheet" href="centauro-style.css" type="text/css" media="all">
    <style>
        .input-text
        {
            padding: 3px 8px;
            font-size: 1.7em;
            line-height: 100%;
            height: 1.7em;
            outline: 0;
            margin: 0;
            background-color: #fff;
        }
        .input-button
        {
            background-color: rgb(46, 162, 204);
            border: 1px solid rgb(0, 116, 162);
            border-radius: 3px;
            box-shadow: rgba(120, 200, 230, 0.498039) 0px 1px 0px 0px inset, rgba(0, 0, 0, 0.14902) 0px 1px 0px 0px;
            box-sizing: border-box;
            color: rgb(255, 255, 255);
            cursor: pointer;
            display: inline-block;
            font-family: 'Open Sans', sans-serif;
            font-size: 13px;
            font-weight: normal;
            height: 30px;
            width: 70px;
            word-spacing: 0;
            writing-mode: lr-tb;
        }
        .wrap-sms
        {
            width: 80%;
            margin-left:auto;
            margin-right:auto;
        }
        </style>
    <script src="jquery.js"></script>
</head>

<body>
<div class="wrap-sms">
    <h1>Compartir <?php echo $titulo;?></h1>
    <!--h3>link <?php echo $link;?></h3-->
<?php
if ($msg!="")
{
    $class = "";
    if ($msg=="No")
    {
        $msg = "Usted no puede enviar mas mensajes";
        $warning = " class='warning'";
    }
    elseif($msg=="Error")
    {
        $msg = "Por favor verifique sus datos, el mensaje no pudo ser enviado";
        $warning = " class='warning'";
    }
    else
    {
        $msg = "Su mensaje ha sido enviado correctamente";
    }
    echo "<div id='temp-message'".$warning.">".$msg."</div>
			<script>\n
			jQuery('#temp-message').delay(3000).fadeOut();\n
			</script>\n
			";
}
?>
        <form method="post">
            <p><input type='text' placeholder="Introduzca su nombre" name='nombre' class='admin-width input-text' maxlength="12"></p>
            <p><input type='text' placeholder="Número de telefono del destinatario" name='telefono' class='admin-width input-text' maxlength="12"></p>
            <p><input type='hidden' name='action' value='send-sms'></p>
            <p><input type='hidden' name='link' value='<?php echo $link;?>'></p>
            <p><input type='submit' class="button button-primary button-large input-button"></p>
        </form>
    </div>
</body>
</html>
