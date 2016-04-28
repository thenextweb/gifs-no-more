<?php

const VIDEO_TYPE_MP4 = 'mp4';
const VIDEO_TYPE_OGG = 'ogv';
const VIDEO_TYPE_WEBM = 'webm';

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Gifs-No-More
 * @author     The Next Web <sysadmins@thenextweb.com>
 */

class Gifsnomore {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    private $conversion_response_pending_meta_key = 'gifsnomore_conversion_pending';

    /**
     * All available video types.
     */
    protected $video_types;

    protected $lazy_loading = false;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'gifs-no-more';
        $this->version = '1.0.0';

        $this->video_types = [
            VIDEO_TYPE_WEBM,
            VIDEO_TYPE_MP4,
//             VIDEO_TYPE_OGG,
        ];

        $this->load_dependencies();
//         $this->define_admin_hooks();
//         $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
     * - Plugin_Name_Admin. Defines all hooks for the admin area.
     * - Plugin_Name_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/gifsnomore-loader.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/gifsnomore-public.php';

        $this->loader = new Gifsnomore_Loader();

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Gifsnomore_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Plugin_Name_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->add_filter('the_content', $this, 'replace_gifs', 50);
        $this->loader->add_action('add_attachment', $this, 'add_attachment');
        $this->loader->add_action('edit_attachment', $this, 'edit_attachment');
        $this->loader->add_action('delete_attachment', $this, 'delete_attachment');
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Find all GIF images on the content and replace them with their video equivalent.
     *
     * @todo: add (optional) lazy loading for these videos
     */
    public function replace_gifs($content)
    {
        // Leave the feeds since we cannot be sure if they support HTML5 video
        if (is_feed()) {
            return $content;
        }

        // Loading post content as a DOM document
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);// Supresses the XML warnings for HTML bad format
        $dom->loadHTML('<?xml version="1.0" encoding="UTF-8"?>'.$content);

        // Look for all images
        $images = $dom->getElementsByTagName('img');
        // Auxiliary node to be cloned when wrapping images with .alignnone class.
        $new_video_wrap = $dom->createElement('video');
        $new_video_wrap->setAttribute('class', 'gifsnomore');
        $new_video_wrap->setAttribute('autoplay', '');
        $new_video_wrap->setAttribute('loop', '');
        $new_video_wrap->setAttribute('muted', '');
        $source = $dom->createElement('source');

        // in order to modify the array and keep iterating it must be done backwards, @see http://php.net/manual/en/class.domnodelist.php#83390
        $length = $images->length;
        for($i = $length-1; $i >= 0; $i--) {
            $image = $images->item($i);
            $img_src = $image->getAttribute('src');
            // Check for lazy loading images
            if (empty($img_src) or (strpos($img_src, 'data:image') !== false)) {
                $img_src = $image->getAttribute('data-src');
            }
            $img_class_name = $image->getAttribute('class');
            if (preg_match("/.*\.gif$/", $img_src, $matches)) {
                error_log("$i Replacing $img_src");
                $wrapper_clone = $new_video_wrap->cloneNode(true);
                $wrapper_clone->setAttribute("class", $wrapper_clone->getAttribute('class') . " $img_class_name");
                foreach($this->video_types as $video_type) {
                    $source_clone = $source->cloneNode(true);
                    $source_clone->setAttribute('type', "video/$video_type");
                    if ($this->lazy_loading) {
                        $source_clone->setAttribute('data-src', preg_replace('/gif$/', $video_type, $img_src));
                    } else {
                        $source_clone->setAttribute('src', preg_replace('/gif$/', $video_type, $img_src));
                    }
                    $wrapper_clone->appendChild($source_clone);
                }
                // This breaks the autoloading, TODO: fix it on the JS side
                if(isset($image->parentNode->tagName) && $image->parentNode->tagName == 'figure') {
                    $figure_node = $image->parentNode;
                    $figure_node->parentNode->replaceChild($wrapper_clone, $figure_node);
                } else {
                    $image->parentNode->replaceChild($wrapper_clone, $image);
                }
            }
        }

        // DOMDocument creates the whole html structure, but we are only interested in the 'body' tag children.
        $content = '';
        $childrenNodes = $dom->getElementsByTagName('body')->item('0')->childNodes;
        if ($childrenNodes->length != 0) {
            foreach ($childrenNodes as $child) {
                $content .= $dom->saveHTML($child);
            }
        }

        return $content;
    }

    public function add_attachment($post_id)
    {
        error_log("Add attachment: $post_id");
        return $this->try_to_convert($post_id);
    }

    public function edit_attachment($post_id)
    {
        error_log("Edit attachment $post_id");
        return $this->try_to_convert($post_id);
    }

    public function delete_attachment($post_id)
    {
        error_log("delete attachment $post_id");
    }

    private function try_to_convert($attachment_id)
    {
        if ( ! $this->mime_type_check( $attachment_id ) ) {
            error_log("Mime type did not match!");
            return;
        }

        // Convert and return the result!
        $this->convert_files($attachment_id);
    }

    /**
     * Indicate whether the attachment can be converted to video.
     *
     * @param int $attachment_id the ID of the attachment.
     * @return bool true if the attachment is of the proper type, false otherwise.
     */
    public function mime_type_check( $attachment_id )
    {
        return 'image/gif' === get_post_mime_type( $attachment_id );
    }


    private function convert_files($attachment_id)
    {
        $attachment_path = $this->find_attachment_path($attachment_id);
        if($attachment_path) {
            foreach($this->video_types as $video_type) {
                $command = realpath(__DIR__."/../bin/gif2$video_type.sh");
                // TODO: check whether shell_exec is allowed or not
                $cmd = $this->build_command($command, $attachment_path);
                $output = shell_exec($cmd);
            }
        }
    }

    private function find_attachment_path($attachment_id)
    {
        $file_path = get_attached_file( $attachment_id );
        if ( is_file( $file_path ) ) {
            return $file_path;
        }
        return false;
    }

    private function build_command($command, $attachment_path)
    {
        return "$command " . $this->shellargs("$attachment_path") . ' 2>&1';
    }

    /**
     * Replacement for escapeshellarg() that won't kill non-ASCII characters and grabs stderr.
     *
     * @param string $arg
     *
     * @return string
     */
    private function shellargs( $arg )
    {
        return "'" .  str_replace("'", "'\"'\"'", $arg) . "'";
    }

}