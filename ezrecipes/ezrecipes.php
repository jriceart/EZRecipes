<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.ricedev.com
 * @since             1.0.0
 * @package           Ezrecipes
 *
 * @wordpress-plugin
 * Plugin Name:       EZRecipes
 * Plugin URI:        www.ricedev.com
 * Description:       An easy to use Recipe Plugin that creates a recipe custom post type and incorporates your recipes into the main wordpress feed. Developed with my wife in mind, now she has one less excuse to start documenting her recipes. 
 * Version:           1.6.0
 * Author:            Jrice
 * Author URI:        www.ricedev.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ezrecipes
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EZRECIPES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ezrecipes-activator.php
 */
function activate_ezrecipes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ezrecipes-activator.php';
	Ezrecipes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ezrecipes-deactivator.php
 */
function deactivate_ezrecipes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ezrecipes-deactivator.php';
	Ezrecipes_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ezrecipes' );
register_deactivation_hook( __FILE__, 'deactivate_ezrecipes' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ezrecipes.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ezrecipes() {

	$plugin = new Ezrecipes();
	$plugin->run();

}
run_ezrecipes();


 
add_action( 'init', 'recipe_custom_post_type');
add_filter( 'post_updated_messages', 'recipes_messages' );

    
    function recipe_custom_post_type()  {
        //Specify Recipe Messages
        $labels = array(
            'name'                      =>  'Recipes',
            'singular_name'             =>  'Recipe',
            'menu_name'                 =>  'Recipe',
            'name_admin_bar'            =>  'Recipe',
            'add_new'                   =>  'Add New',
            'add_new_item'              =>  'Add New Recipe',
            'edit_item'                 =>  'New Recipe',
            'view_item'                 =>  'View Recipe',
            'all_items'                 =>  'All Recipes',
            'search_items'              =>  'Search Recipes',
            'parent_item_colon'         =>  'Parent Recipes:',
            'not_found'                 =>  'No Recipes Found:(',
            'not_found_in_trash'        =>  'No Recipes Found in Trash:(',
            'uploaded_to_this_item'     =>  'Uploaded To This Recipe',
            'item_published'            =>  'Congrats, New Recipe Published!',
            'item_published_privately'  =>  'Recipe Published Privately.',
            'item_reverted_to_draft'    =>  'Recipe Headed Back To The Kitchen!',
            'item_scheduled'            =>  'Recipe Timer Set!',
            'item_updated'              =>  'Recipe Updated!'
        );

        $args = array(
            'public'        => true,
            'labels'        =>  $labels,
            'rewrite'       =>  array(  'slug'  =>  'recipe'),
            'has_archive'   =>  true,
            'menu_position' =>  20,
            'menu_icon'     =>  'dashicons-carrot',
            'taxonomies'    =>  array(  'post_tag' ,    'category'),
            'supports'      =>  array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' )
        );

            register_post_type( 'recipe',   $args);

            
        
    }




    function recipes_messages(  $messages   )  {
        $post   =   get_post();

        $messages['recipe'] =   array(
            0   =>  '',
            1   =>  'Recipe Updated.',
            2   =>  'Custom Field Updated.',
            3   =>  'Custom Field Deleted.',
            4   =>  'Recipe Updated.',
            5   =>   isset( $_GET['revision'] ) ? sprintf( 'Recipe restored to revision from %s',wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, 
            6   =>  'Recipe Published.',
            7   =>  'Recipe Saved.',
            8   =>  'Recipe Submitted.',
            9   =>  sprintf('Recipe scheduled for: <strong>%1$s</strong>.',
                        date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) )  ),
            10  =>  'Recipe draft updated.'    
        );

        return $messages;
    }

    

    function recipe_loop( $query ) {
        //if on home page, if main query => add recipe to posts
        if ( is_home() && $query->is_main_query() )
        $query->set( 'post_type', array( 'post', 'recipe') );
        return $query;
        }
        add_filter( 'pre_get_posts', 'recipe_loop' );

    function my_rewrite_flush() {
        //Permalinks Flush
        my_cpt_init();
        
        flush_rewrite_rules();
        }
        register_activation_hook( __FILE__, 'my_rewrite_flush' );    

?>