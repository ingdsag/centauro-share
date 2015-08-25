<?php
	if (isset($_REQUEST['action']) && $_REQUEST['action']=="guardar_compartir")
	{
		update_option( 'fb-share', 'off');
		update_option( 'tw-share', 'off');
		update_option( 'gplus-share', 'off');
		update_option( 'mail-share', 'off');
		update_option( 'sms-share', 'off');
		update_option( 'ubicar-derecha', 'off');
		if (isset($_REQUEST['intentos-sms'])!='' && is_numeric($_REQUEST['intentos-sms']))
		{
			update_option( 'intentos-sms', wp_filter_nohtml_kses($_REQUEST['intentos-sms']));
		}
		if (isset($_REQUEST['texto-twitter'])!='' && ($_REQUEST['texto-twitter']))
		{
			update_option( 'texto-twitter', wp_filter_nohtml_kses($_REQUEST['texto-twitter']));
		}
		if (isset($_REQUEST['titulo-correo'])!='' && ($_REQUEST['titulo-correo']))
		{
			update_option( 'titulo-correo', wp_filter_nohtml_kses($_REQUEST['titulo-correo']));
		}
		if (isset($_REQUEST['texto-correo'])!='' && ($_REQUEST['texto-correo']))
		{
			update_option( 'texto-correo', wp_filter_nohtml_kses($_REQUEST['texto-correo']));
		}
		if (isset($_REQUEST['texto-mensaje'])!='' && ($_REQUEST['texto-mensaje']))
		{
			update_option( 'texto-mensaje', wp_filter_nohtml_kses($_REQUEST['texto-mensaje']));
		}

		if (isset($_REQUEST['ubicar-derecha']))
		{
			update_option( 'ubicar-derecha', 'checked');
		}
		if (isset($_REQUEST['fb-share']))
		{
			update_option( 'fb-share', 'checked');
		}
		if (isset($_REQUEST['tw-share']))
		{
			update_option( 'tw-share', 'checked');
		}
		if (isset($_REQUEST['gplus-share']))
		{
			update_option( 'gplus-share', 'checked');
		}
		if (isset($_REQUEST['mail-share']))
		{
			update_option( 'mail-share', 'checked');
		}
		if (isset($_REQUEST['sms-share']))
		{
			update_option( 'sms-share', 'checked');
		}



		/*update_option( 'sms_ven_mensaje_usuario', wp_filter_nohtml_kses($_REQUEST['sms_ven_mensaje_usuario']));
		update_option( 'sms_ven_mensaje_post', wp_filter_nohtml_kses($_REQUEST['sms_ven_mensaje_post']));*/
	}

	$intentos_sms = get_option('intentos-sms');
	$texto_twitter = get_option('texto-twitter');
	if ($texto_twitter=="")
	{
		$texto_twitter = "Vean este enlace! %ENLACE%";
		update_option( 'texto-twitter', $texto_twitter);
	}

	$titulo_correo = get_option('titulo-correo');
	if ($titulo_correo=="")
	{
		$titulo_correo = "Mira este enlace!";
		update_option( 'titulo-correo', $titulo_correo);
	}
	$texto_correo = get_option('texto-correo');
	if ($texto_correo=="")
	{
		$texto_correo = "Mira este enlace! %ENLACE%";
		update_option( 'texto-correo', $texto_correo);
	}
	$texto_mensaje = get_option('texto-mensaje');
	if ($texto_mensaje=="")
	{
        $texto_mensaje = "Mira este enlace! %ENLACE%, enviado por %NOMBRE%";
		update_option( 'texto-mensaje', $texto_mensaje);
	}
	$ubicar_derecha = get_option('ubicar-derecha');
	$fb_share = get_option('fb-share');
	$tw_share = get_option('tw-share');
	$gplus_share = get_option('gplus-share');
	$mail_share = get_option('mail-share');
	$sms_share = get_option('sms-share');

?>
<div class="wrap">
	<h2>Opciones de compartir</h2>


	<?php
		if (isset($_REQUEST['action']) && $_REQUEST['action']=="guardar_compartir")
		{
			echo "<div id='temp-message'>Las configuraciones han sido almacenadas exitosamente</div>
			<script>\n
			jQuery('.wrap #temp-message').delay(3000).fadeOut();\n
			</script>\n
			";
		}
	?>
	<form method='post'>
		<div class='left-admin'>
			<h3>Opciones Generales</h3>
            <p><label><input type='checkbox' name='ubicar-derecha' <?php echo $ubicar_derecha?>> Ubicar a la derecha</label></p>
			<h3>Facebook</h3>
			<p><label><input type='checkbox' name='fb-share' <?php echo $fb_share?>> Compartir en Facebook</label></p>
			<h3>Twitter</h3>
			<p>Texto de Twitter</p>
			<p><input type='text' name='texto-twitter' value='<?php echo $texto_twitter?>' class='admin-width'></p>
			<small>Usa %ENLACE% para posicionar el enlace a la pagina actual</small>
			<p><label><input type='checkbox' name='tw-share' <?php echo $tw_share?>> Compartir en Twitter</label></p>

			<h3>Google Plus</h3>
			<p><label><input type='checkbox' name='gplus-share' <?php echo $gplus_share?>> Compartir en Google Plus</label></p>
		</div>

		<div class='left-admin'>
			<h3>Correo electrónico</h3>
			<p><input type='text' name='titulo-correo' value='<?php echo $titulo_correo?>' class='admin-width'></p>
			<p><input type='text' name='texto-correo' value='<?php echo $texto_correo?>' class='admin-width'></p>
			<small>Usa %ENLACE% para posicionar el enlace a la pagina actual</small>
			<p><label><input type='checkbox' name='mail-share' <?php echo $mail_share?>> Compartir por Correo</label></p>

			<h3>Mensajeria SMS</h3>
			<p>Cantidad de intentos por ip</p>
			<p><input type='text' name='intentos-sms' value='<?php echo $intentos_sms?>' class='admin-width'></p>
            <p><input type='text' name='texto-mensaje' value='<?php echo $texto_mensaje?>' class='admin-width'></p>
            <small>Usa %ENLACE% para posicionar el enlace a la pagina actual y usa %NOMBRE% para agregar el nombre del
                destinatario en tu mensaje (este es ingresado por el emisor del mensaje)</small>
			<p><label><input type='checkbox' name='sms-share' <?php echo $sms_share?>> Compartir por sms</label></p>
		</div>
		<div style='clear:both;'></div>
		<br>
		<br>
		<br>
		<input type='hidden' name='action' value='guardar_compartir' />
		<input type='submit' class='button-primary' value='Guardar'>
	</form>


	<div class='header-settings'>
	</div>
</div>
