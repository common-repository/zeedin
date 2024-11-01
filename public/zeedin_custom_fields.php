<?php

class zeedin_custom_fields {

	/**
	 * Maintains a value to the text field ID for serialization.
	 *
	 * @access private
	 * @var    string
	 */
	private $textfield_id;

	/**
	 * Initializes the class and the instance variables.
	 */
	public function __construct() {
	}

	/**
	 * Initializes the hooks for adding the text field and saving the values.
	 */
	public function init() {

		function zeedin_my_custom_checkout_field( $checkout ) {

			$option1 = get_option( 'zeedin_plugin_setting_nombre' );
			$option2 = get_option( 'zeedin_plugin_setting_correo' );
	    echo '<div id="zeedin_my_custom_checkout_field"><h3>' . __('Programa de embajadores') . '</h3>';
			echo '<div id="zeedin_my_custom_checkout_field2"><p>' . __('Aplica a nuestro programa de embajadores compartiendo tu usuario de Instagram, podrás acceder a descuentos, items y promociones exclusivas. Revisa tu correo donde te enviaremos los detalles del programa en alianza con Zeedin.') . '</p>';
			echo '</div>';

	    woocommerce_form_field( 'zeedin_InstaUserName', array(
	        'type'          => 'text',
	        'class'         => array('my-field-class form-row-wide'),
	        'label'         => __('Usuario de Instagram'),
	        'placeholder'   => __('@usuario'),
					'required'			=> false,
				), $checkout->get_value( 'zeedin_InstaUserName' ));

	    echo '</div>';

		}

		function zeedin_my_custom_checkout_field_process() {
	    // Check if set, if its not set add an error.
	    if ( ! sanitize_text_field($_POST['zeedin_InstaUserName']) ){
				//wc_add_notice( __( 'Please fill all of the required fields' ), 'error' );
			}
		}

		function zeedin_my_custom_checkout_field_update_order_meta( $order_id ) {
	    if ( ! empty( sanitize_text_field($_POST['zeedin_InstaUserName']) ) ) {
	        update_post_meta( $order_id, 'Instagram', sanitize_text_field( $_POST['zeedin_InstaUserName'] ) );
	    }
		}

		function saving_checkout_cf_data( $order_id ) {

			$zeedin_first_name = 'NA';
			$zeedin_last_name = '';
			$zeedin_email = 'NA';
			$zeedin_phone = 'NA';
			$zeedin_insta_user = 'NA';
			$zeedin_user = 'NA';
			$zeedin_correo = 'NA';
			$zeedin_nombre = '';

			if ( ! sanitize_text_field($_POST['zeedin_InstaUserName']) ){
			}else{
				if ( ! empty( sanitize_text_field($_POST['billing_first_name']) ) ){
		        $zeedin_first_name = sanitize_text_field($_POST['billing_first_name']);
						$zeedin_nombre = "'". $zeedin_first_name. "'";
				}

				if ( ! empty( sanitize_text_field($_POST['billing_last_name']) ) ){
		        $zeedin_last_name = sanitize_text_field($_POST['billing_last_name']);
						$zeedin_nombre = "'". $zeedin_first_name. " ". $zeedin_last_name. "'";
				}

				if ( ! empty( sanitize_email($_POST['billing_email']) ) ){
		        $zeedin_email = sanitize_email($_POST['billing_email']);
						$mail = $zeedin_email;
						$zeedin_email = "'". $zeedin_email. "'";
				}

				if ( ! empty( sanitize_text_field($_POST['billing_phone']) ) ){
		        $zeedin_phone = sanitize_text_field($_POST['billing_phone']);
						$zeedin_phone = "'". $zeedin_phone. "'";
				}

				if ( ! empty( sanitize_text_field($_POST['zeedin_InstaUserName']) )){
		        $zeedin_insta_user = sanitize_text_field($_POST['zeedin_InstaUserName']);
						$zeedin_insta_user = "'". $zeedin_insta_user. "'";
				}

				if(get_option( 'zeedin_plugin_setting_nombre' )){
					$zeedin_user = get_option( 'zeedin_plugin_setting_nombre' );
					$zeedin_user = "'". $zeedin_user. "'";
				}
				if(get_option( 'zeedin_plugin_setting_correo' )){
					$zeedin_correo = get_option( 'zeedin_plugin_setting_correo' );
					$zeedin_correo = "'". $zeedin_correo. "'";
				}

				$conn = mysqli_connect("zdatabase.clcxxkprrhr1.us-east-2.rds.amazonaws.com",  "zeedin2589", "FuKgCK5rkUlvFEDxXISn", "zeedindb");

				if( $conn === false )
				{
						 die('Could not connect: ' . mysqli_connect_error());
				}

				/*PluginData table processing*/

				$val = date('Y-m-d');
				$date = "'". $val. "'";

				$id = md5(uniqid(rand(), true));
				$id = "'". $id. "'";

				/* Insert new values into the table. */
				$sql = "INSERT INTO PluginData
								(Id,
								 UserId,
								 CorreoMarca,
								 Nombre,
								 Correo,
								 Telefono,
								 nombreUsuarioI,
								 Fecha,
								 NumOfFollowers,
							   engagementRate,
							   PInfluencia,
							   profileUrl)
								VALUES
								($id, $zeedin_user, $zeedin_correo, $zeedin_nombre
									,$zeedin_email , $zeedin_phone, $zeedin_insta_user, $date, 0, 0, 0, NULL)";

				$stmt = mysqli_query($conn,$sql) or die($conn->error);
				if ($stmt) {
					$url = 'http://creators.zeedin.co/Home/sendInvitation?correo='.$mail;
					$opts = array(
					'http'=>array(
						'method'=>"GET",
						'header'=>"User-Agent:MyAgent/1.0\r\n"
					)
					);

					$context = stream_context_create($opts);

					/* Sends an http request zeedin.co to send welcoming email from zeedin's server mail */
					$fp = fopen($url, 'r', false, $context);
					fpassthru($fp);
					fclose($fp);
				} else {
						die($conn->error);
				}

				/* Free connection resources. */
				mysqli_close($conn);
			}
		}

		// Hooked in function to override default values - $fields is passed via the filter!
		function custom_override_checkout_fields( $fields ) {
		     //$fields['billing']['billing_first_name']['required'] = true;
		     //$fields['billing']['billing_last_name']['required'] = true;
				 //$fields['billing']['billing_email']['required'] = true;
				 //$fields['billing']['billing_phone']['required'] = true;
		     return $fields;
		}

		/**
		*Add the custom fields
		*/
		add_action( 'woocommerce_after_order_notes', 'zeedin_my_custom_checkout_field' );

		/**
		 * Process the checkout
		 */
		add_action('woocommerce_checkout_process', 'zeedin_my_custom_checkout_field_process');

		/**
		*Hook in to checkout fields to change requirements
		*/
		add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

		/**
		 * Update the order meta with field value
		 */
		add_action( 'woocommerce_checkout_update_order_meta', 'zeedin_my_custom_checkout_field_update_order_meta' );

		/**
		*Save the data to the database
		*/
		add_action( 'woocommerce_checkout_update_order_meta', 'saving_checkout_cf_data');

	}



}
