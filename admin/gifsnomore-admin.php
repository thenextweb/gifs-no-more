<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Gifs-No-More
 * @author     The Next Web <sysadmins@thenextweb.com>
 */
class Gifsnomore_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

//         wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gifsnomore-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
        * This function is provided for demonstration purposes only.
        *
        * An instance of this class should be passed to the run() function
        * defined in Plugin_Name_Loader as all of the hooks are defined
        * in that particular class.
        *
        * The Plugin_Name_Loader will then create the relationship
        * between the defined hooks and the functions defined in this
        * class.
        */

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gifsnomore-admin.js', array( 'jquery' ), $this->version, false );

    }

    public function add_plugin_menu()
    {
        add_options_page( 'GifsNoMore Options', 'GifsNoMore', 'manage_options', 'gifs-no-more', [$this, 'my_plugin_options'] );
    }

    public function register_my_settings()
    {
        register_setting( 'gifsnomore_options', 'gifsnomore', [$this, 'validate_options'] );
    }

    public function my_plugin_options() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        include __DIR__ . '/partials/gifsnomore-admin-display.php';
    }

    public function validate_options($input)
    {
        $input['transform_all'] =   isset($input['transform_all']) && in_array(intval($input['transform_all']), [0, 1]) ?
                                    $input['transform_all'] :
                                    0;
        if (isset($input['from_date'])) {
            $d = DateTime::createFromFormat('Y-m-d', $input['from_date']);
            $input['from_date'] = $d && $d->format('Y-m-d') == $input['from_date'] ? $input['from_date'] : date('Y-m-d');
        } else {
            $input['from_date'] = date('Y-m-d');
        }
        return $input;
    }

}
