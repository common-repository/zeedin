<?php
/**
*@package ZeedinCo
* @wordpress-plugin
* Plugin Name:       Zeedin
* Plugin URI:        http://zeedin.co/plugin
* Description:       Connects your woocommerce store with your Zeedin.co account and adds a custom field to the checkout to include social media information.
* Version:           1.0.0
* Author:            Zeedin
* Author URI:        http://zeedin.co
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!defined( 'WPINC')) {
    die;
}

include_once 'public/zeedin_custom_fields.php';

/**
 * Start the plugin.
 */
function zeedin_wc_input_start() {

	if ( is_admin() ) {
	} else {
		$custom_checkout = new zeedin_custom_fields();
		$custom_checkout->init();
	}
}

/*Settings page*/
function zeedin_register_settings() {
    add_settings_section( 'zeedin_zplugin_options', 'Conecta tu cuenta', "zeedin_plugin_section_text", "zeedin_myNew_plugin");
    register_setting( 'zeedin_zplugin_options', 'zeedin_plugin_setting_nombre');
    register_setting( 'zeedin_zplugin_options', 'zeedin_plugin_setting_correo');

    add_settings_field( 'zeedin_plugin_setting_nombre', 'Nombre completo', 'zeedin_display_plugin_setting_nombre', 'zeedin_myNew_plugin', 'zeedin_zplugin_options' );
    add_settings_field( 'zeedin_plugin_setting_correo', 'Correo registrado en Zeedin', 'zeedin_display_plugin_setting_correo', 'zeedin_myNew_plugin', 'zeedin_zplugin_options' );
}

/*Create section text*/
function zeedin_plugin_section_text() {
    echo '<p>Conecta tu cuenta de zeedin para poder empezar a obtener información de tus usuarios</p>';
}

/*plugin setting name*/
function zeedin_display_plugin_setting_nombre() {
    ?>
        <input type="text" name="zeedin_plugin_setting_nombre" id="zeedin_plugin_setting_nombre" value="<?php echo esc_html(get_option('zeedin_plugin_setting_nombre')); ?>" />
    <?php
}

/*plugin setting email*/
function zeedin_display_plugin_setting_correo() {
    ?>
        <input type="text" name="zeedin_plugin_setting_correo" id="zeedin_plugin_setting_correo" value="<?php echo esc_html(get_option('zeedin_plugin_setting_correo')); ?>" />
    <?php
}

/*Setup settings page*/
function zeedin_add_settings_page() {
    add_options_page( 'Zeedin', 'Zeedin', 'manage_options', 'zeedin_myNew_plugin', 'zeedin_render_plugin_settings_page' );
}

/*Render settings page*/
function zeedin_render_plugin_settings_page() {
    ?>
    <h2>Opciones del plugin</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'zeedin_zplugin_options' );
        do_settings_sections( 'zeedin_myNew_plugin' ); ?>
        <input onclick="alert('Tu cuenta se ha conectado')" name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

/*Personalize the dashboard*/
function zeedin_custom_dashboard_widgets() {
    global $wp_meta_boxes;

    wp_add_dashboard_widget('custom_help_widget', 'Zeedin', 'zeedin_custom_dashboard_help');
}

/*Dashboard HTML*/
function zeedin_custom_dashboard_help() {

    $conn = mysqli_connect("zdatabase.clcxxkprrhr1.us-east-2.rds.amazonaws.com",  "zeedin4973", "jfiZIwlfMzCVCMvhVWGt", "zeedindb");

    if( $conn === false )
    {
             die('Could not connect: ' . mysqli_connect_error());
    }

    /* Search values from the server table, from the last 30 days */
    $today = date('Y-m-d');
    $thirtyDays = date('Y-m-d', strtotime($today. '- 30 days'));
    $thirtyDays = strVal($thirtyDays);
    $myemail = get_option( 'zeedin_plugin_setting_correo' );
    $sql = "SELECT COUNT(*) CorreoMarca
             FROM PluginData
             WHERE CorreoMarca='$myemail'
             AND DATE(Fecha) > '$thirtyDays'";

    $result = mysqli_query($conn,$sql) or die($conn->error);
    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $month = $row["CorreoMarca"];
    }
    } else {
      $month = 'NA';
    }

    if ($result) {
    } else {
            die($conn->error);
    }

    /* Search total values from the server table */
    $sql2 = "SELECT COUNT(*) CorreoMarca
             FROM PluginData
             WHERE CorreoMarca='$myemail'
             LIMIT 1";

    $result2 = mysqli_query($conn,$sql2) or die($conn->error);
    if ($result2->num_rows > 0) {
    // output data of each row
    while($row2 = $result2->fetch_assoc()) {
      $total = $row2["CorreoMarca"];
    }
    } else {
      $total = 'NA';
    }

    if ($result2) {
    } else {
            die($conn->error);
    }

    /* Free connection resources. */
    mysqli_close($conn);

    if(get_option( 'zeedin_plugin_setting_nombre' ) && get_option( 'zeedin_plugin_setting_correo' )){
        echo '<style>
    		.zeedin-pg-box{
    			width: 100%;
    			background: #fff;
    		}

    		.zeedin-container{
    			padding: 10px 20px 20px;
    			display: block;
    		}

    		.centered{
    			margin: 10px auto;
    			text-align: center;
    			float: none;
    		}

    		.alert-c{
    			color: orangered;
    		}

    		.alert-g{
    			color: green;
    		}

    		.button, .zeedin-button{
    			background: #364ee7;
    			border-color: #364ee7;
    			color: #fff;
    			text-decoration: none;
    			text-shadow: none;
    			display: block;
    			text-decoration: none;
    			font-size: 13px;
    			line-height: 2.15384615;
    			min-height: 30px;
    			margin: 0 auto !important;
    			padding: 0 10px;
    			cursor: pointer;
    			border-width: 1px;
    			border-style: solid;
    			border-radius: 3px;
    			white-space: nowrap;
    			box-sizing: border-box;
    			max-width: 220px;
    		}

    		.zeed-metricas{
    			width: 100%;
    			display: flex;
    			margin-top: 25px;
    		}

    		.zeed-metricas div{
    			border: 1px solid #ddd;
    			padding: 15px;
    			flex: 1;
    		}

    		.zeed-metricas div:first-child{
    			border-right: 0px;
    		}

    		.zeed-titulo{
    			font-size: 14px;
    			margin: 5px 0;
    		}

    		.zeed-number{
    			font-size: 32px;
    			margin: 0px;
    			font-weight: 700;
    		}


    		.zeedin-button{
    			text-align: center;
    			font-weight: bold;
    			padding: 10px 20px;
    			margin-top: 20px !important;
    		}

    	</style>

    	<div class="zeedin-pg-box">
    		<div class="zeedin-container">

    			<h2 class="centered">¡Zeedin está conectado!</h2>

    			<div class="zeed-metricas">
    				<div>
    					<p class="zeed-titulo">Registros último mes</p>
    					<p class="zeed-number">'. $month. '</p>
    				</div>
    				<div>
    					<p class="zeed-titulo">Registros totales</p>
    					<p class="zeed-number">'. $total. '</p>
    				</div>
    			</div>
    			<p style="margin-top: 20px" class="centered">Administra tus embajadores:</p>
    			<a class="zeedin-button" href="https://creators.zeedin.co/User/Embajadores" target="_blank">Ir al Dashboard</a>
    		</div>
    	</div>';
    }else{
        echo '<style>
    		.zeedin-pg-box{
    			width: 365px;
    			background: #fff;
    		}

    		.zeedin-container{
    			padding: 10px 20px 20px;
    			display: block;
    		}

    		.centered{
    			margin: 10px auto;
    			text-align: center;
    			float: none;
    		}

    		.alert-c{
    			color: orangered;
    		}

    		.alert-g{
    			color: green;
    		}

    		.button, .zeedin-button{
    			background: #364ee7;
    			border-color: #364ee7;
    			color: #fff;
    			text-decoration: none;
    			text-shadow: none;
    			display: block;
    			text-decoration: none;
    			font-size: 13px;
    			line-height: 2.15384615;
    			min-height: 30px;
    			margin: 0 auto !important;
    			padding: 0 10px;
    			cursor: pointer;
    			border-width: 1px;
    			border-style: solid;
    			border-radius: 3px;
    			white-space: nowrap;
    			box-sizing: border-box;
    			max-width: 220px;
    		}

    		.zeed-metricas{
    			width: 100%;
    			display: flex;
    			margin-top: 25px;
    		}

    		.zeed-metricas div{
    			border: 1px solid #ddd;
    			padding: 15px;
    			flex: 1;
    		}

    		.zeed-metricas div:first-child{
    			border-right: 0px;
    		}

    		.zeed-titulo{
    			font-size: 14px;
    			margin: 5px 0;
    		}

    		.zeed-number{
    			font-size: 32px;
    			margin: 0px;
    			font-weight: 700;
    		}


    		.zeedin-button{
    			text-align: center;
    			font-weight: bold;
    			padding: 10px 20px;
    			margin-top: 20px !important;
    		}

    	</style>

    	<div class="zeedin-pg-box">
    		<div class="zeedin-container">
    			<h2 class="centered">Zeedin <span class="alert-c">no está conectado!</span></h2>
    			<a href="/wp-admin/options-general.php?page=zeedin_myNew_plugin" class="zeedin-button">Conectar a tu cuenta Zeedin</a>
    		</div>
    	</div>';
    }
}

/*Call all required hooks*/
add_action( 'plugins_loaded', 'zeedin_wc_input_start' );

add_action( 'admin_init', 'zeedin_register_settings' );

add_action( 'admin_menu', 'zeedin_add_settings_page' );

add_action('wp_dashboard_setup', 'zeedin_custom_dashboard_widgets');
