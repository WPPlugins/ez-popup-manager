<?php
/*
Plugin Name: EZ Popup Manager
Plugin URI: http://wordpress.org/extend/plugins/EZ-popup-manager/
Description: Adds a promotional popup in homepage.
Version: 1.2
Author: Darren Chow
Author URI: http://www.darrenchow.com
License: GPL2
*/


define( 'SPM_PLUGIN_VERSION', '1.0' );


add_action('admin_init', 'simple_popup_manager_init' );
add_action('admin_menu', 'simple_popup_manager_add_page');


load_plugin_textdomain('ez-popup-manager', false, basename( dirname( __FILE__ ) ) . '/languages/');


function simple_popup_manager_init(){
	
	register_setting( 'simple_popup_manager_options', 'simple_popup_manager_fields', 'simple_popup_manager_validate' );
	
}


function simple_popup_manager_add_page() {
	add_options_page(__('Titre page doption','ez-popup-manager'), __('page doption dans menu','ez-popup-manager'), 'manage_options', 'ez_popup_manager', 'simple_popup_manager_do_page');
}


function simple_popup_manager_do_page() {
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2><?php echo __('Titre page doption','ez-popup-manager');?></h2>
		<p><?php echo __('Description du plugin','ez-popup-manager');?></p>
		<form method="post" action="options.php">
			<?php settings_fields('simple_popup_manager_options'); ?>
			<?php $options = get_option('simple_popup_manager_fields'); ?>
            <?php $posttypes = get_post_types(array('public'=>true)) ;?> 
			<table class="form-table">
				<tr valign="top"><th scope="row"><?php echo __('Activer la popup : ','ez-popup-manager');?></th>
				<td><input name="simple_popup_manager_fields[active]" type="checkbox" value="1" <?php if(!empty($options['active']))checked('1', $options['active']); ?> /></td>
				</tr>

				<tr valign="top"><th scope="row"><?php echo __('Only Show on Frontpage : ','ez-popup-manager');?></th>
				<td><input name="simple_popup_manager_fields[frontpage]" type="checkbox" value="1" <?php if(!empty($options['frontpage']))checked('1', $options['frontpage']); ?> /></td>
				</tr>

				<tr valign="top"><th scope="row"><?php echo __('Debug mode : ','ez-popup-manager');?></th>
				<td><input name="simple_popup_manager_fields[debug]" type="checkbox" value="1" <?php if(!empty($options['debug']))checked('1', $options['debug']); ?> /><?php echo __('No cookie, popup only visible for logged in admin on frontpage','simple-popup-manager');?></td>
				</tr>
				<tr valign="top"><th scope="row"><?php echo __('Bouton de fermeture : ','ez-popup-manager');?></th>
				<td><input name="simple_popup_manager_fields[bouton]" type="checkbox" value="1" <?php if(!empty($options['bouton']))checked('1', $options['bouton']); ?> /></td>
				</tr>
                <tr valign="top"><th scope="row"><?php echo __('Duree du cookie en jour(s) : ','ez-popup-manager');?></th>
				<td><fieldset><input type="number" step="1" min="0" class="small-text" name="simple_popup_manager_fields[cookie]" value="<?php echo ($options['cookie']!='' ? $options['cookie'] :  $options['cookie'] ); ?>" /> <?php echo __('Valeur possible cookie','ez-popup-manager');?></fieldset></td>
				</tr>
                <tr valign="top">
                <th scope="row"><?php echo __('Largeur en pixels : ','ez-popup-manager');?></th>
				<td>
				<fieldset>
				<?php echo __('Largeur en pixels : ','ez-popup-manager');?>
				<input type="number" step="10" min="0" class="small-text" name="simple_popup_manager_fields[largeur]" value="<?php echo ($options['largeur']!='' ? $options['largeur'] :  300 ); ?>" />
				<?php echo __('Hauteur en pixels : ','ez-popup-manager');?></th>
				<input type="number" step="10" min="0" class="small-text" name="simple_popup_manager_fields[hauteur]" value="<?php echo ($options['hauteur']!='' ? $options['hauteur'] :  250 ); ?>" />
				</fieldset>
				</td>
				</tr>
				<tr valign="top"><th scope="row"><?php echo __('Opacite : ','ez-popup-manager');?></th>
				<td><input type="number" step="0.1" min="0" max="1" class="small-text"  name="simple_popup_manager_fields[opacite]" value="<?php echo ($options['opacite']!='' ? $options['opacite'] :  0.5 ); ?>" /></td>
				</tr>
                <tr valign="top"><th scope="row"><?php echo __('Contenu de la popup : ','ez-popup-manager');?></th>
				<td><?php wp_editor($options['contenu'], 'simple_popup_manager_fields[contenu]'); ?></td></tr>
				<tr valign="top"><th scope="row"><?php echo __('Desactiver la fermeture  : ','ez-popup-manager');?></th>
				<td><input name="simple_popup_manager_fields[disableOutside]" type="checkbox" value="1" <?php if(!empty($options['disableOutside']))checked('1', $options['disableOutside']); ?> /></td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}


function simple_popup_manager_validate($input) {
	return $input;
}

/* Files loader */

function simple_popup_manager_js_css(){

$options = get_option('simple_popup_manager_fields');
$options['contenu'] = fake_content_spm($options['contenu']);
	
if( ($options['active']) ){


	if( ($options['frontpage'] && is_home()) || ($options['frontpage'] && is_front_page()) ){
	

		if( $options['debug'] && current_user_can( 'manage_options' ) || !$options['debug'] ){
		//jQuery Cookie
		wp_deregister_script( 'jquery-cookie' );
		wp_register_script( 'jquery-cookie', plugins_url('js/jquery.cookie.js', __FILE__), array( 'jquery' ),SPM_PLUGIN_VERSION);
		wp_enqueue_script( 'jquery-cookie' );
		
	
		//Simple Popup Manager Javascript
		wp_deregister_script( 'simple-popup-manager' );
		wp_register_script( 'simple-popup-manager', plugins_url('js/simple-popup-manager.js', __FILE__), array( 'jquery', 'jquery-cookie' ),SPM_PLUGIN_VERSION);
		wp_enqueue_script( 'simple-popup-manager' );
		
		//options to Javascript
		wp_localize_script( 'simple-popup-manager', 'servername', site_url());
		wp_localize_script( 'simple-popup-manager', 'options', $options );
	
		//stylesheets
		wp_register_style( 'simple_popup_manager-style', plugins_url('css/style.css', __FILE__),SPM_PLUGIN_VERSION);
		wp_enqueue_style( 'simple_popup_manager-style' );
		}

	}
	else if ($options['frontpage'] <> '1' )
	{
		
		if( $options['debug'] && current_user_can( 'manage_options' ) || !$options['debug'] ){
		//jQuery Cookie
		wp_deregister_script( 'jquery-cookie' );
		wp_register_script( 'jquery-cookie', plugins_url('js/jquery.cookie.js', __FILE__), array( 'jquery' ),SPM_PLUGIN_VERSION);
		wp_enqueue_script( 'jquery-cookie' );
		
	
		//Simple Popup Manager Javascript
		wp_deregister_script( 'simple-popup-manager' );
		wp_register_script( 'simple-popup-manager', plugins_url('js/simple-popup-manager.js', __FILE__), array( 'jquery', 'jquery-cookie' ),SPM_PLUGIN_VERSION);
		wp_enqueue_script( 'simple-popup-manager' );
		
		//options to Javascript
		wp_localize_script( 'simple-popup-manager', 'servername', site_url());
		wp_localize_script( 'simple-popup-manager', 'options', $options );
	
		//stylesheets
		wp_register_style( 'simple_popup_manager-style', plugins_url('css/style.css', __FILE__),SPM_PLUGIN_VERSION);
		wp_enqueue_style( 'simple_popup_manager-style' );
		}

	}

}

}
add_action('wp_enqueue_scripts', 'simple_popup_manager_js_css'); 


/*
*	Formate un post_content avec les fonctions coeur de the_content, mais n'est pas accroché par les
*	add_filter('the_content') des plugins
*/
function fake_content_spm($content)
{
	
	$content=wptexturize($content);
	$content=wpautop($content);
	$content=convert_chars($content);
	$content=shortcode_unautop($content);
	$content=do_shortcode($content);
	return $content;
}



?>