<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
    <div class="wrap">
        <h2>GifsNoMore Main Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('gifsnomore_options'); ?>
            <?php $options = get_option(Gifsnomore::$options_key);
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Replace all GIF images from ALL the posts to GIFV <p>(If checked, the date below won't be taken into account.)</p></th>
                    <td><input disabled name="gifsnomore[transform_all]" type="checkbox" value="1" <?php checked('1', $options['transform_all']); ?> /></td>
                    <td><a href="#" disabled class="button-primary">Convert all gifs to video</a></td>
                </tr>
                <tr valign="top"><th scope="row">Replace GIF images from this date on</th>
                    <td><input type="date" name="gifsnomore[from_date]" value="<?php if(isset($options['from_date']) && !empty($options['from_date'])) {echo $options['from_date']; } else { echo date('Y-m-d'); } ?>" /></td>
                </tr>
            </table>
            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>
    <?php
