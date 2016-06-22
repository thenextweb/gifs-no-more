<?php
/**
 * Implements gifsnomore commands to convert gifs from CLI.
 */
class Gifsnomore_Command extends WP_CLI_Command {

    /**
     * Convert the given amount of gifs from your media library
     *
     * ## OPTIONS
     *
     * <limit>
     * : The maximum number of gifs to be searched for and converted into video.
     *
     * [--offset=<offset>]
     * : The offset to be applied when looking for gifs on the DB.
     *
     * ## EXAMPLES
     *
     *     wp convert 100 0
     *
     * @when before_wp_load
     */
    function convert( $args, $assoc_args ) {
//         Gifsnomore::enable_debug();
        $limit = isset($args[0]) && is_numeric($args[0]) ?
                    $args[0] :
                    Gifsnomore::$max_images_to_convert;
        $offset = isset($assoc_args['offset']) && is_numeric($assoc_args['offset']) ?
                    $assoc_args['offset'] :
                    0;
        $converted = Gifsnomore::retrieve_and_convert_all_posts($limit, $offset);
//         Gifsnomore::disable_debug();
        WP_CLI::success( "$converted GIFs have been converted to video." );
    }
}

WP_CLI::add_command( 'gifsnomore', 'Gifsnomore_Command' );
