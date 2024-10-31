<?php
/*
Plugin Name: Outdr Booking Widget
Description: Outdr booking widget for school site integrations
Version:     0.1.5
Author:      Outdr Ltd
Author URI:  https://outdr.com
*/

// recommended minimal PHP tutorial https://learnxinyminutes.com/docs/php/

namespace Outdr;

use \add_action;
use \add_options_page;
use \add_settings_field;
use \add_settings_section;
use \add_shortcode;
use \apply_filters;
use \do_settings_section;
use \get_option;
use \plugins_url;
use \register_setting;
use \settings_fields;
use \submit_button;
use \wp_enqueue_script;
use \wp_enqueue_style;

// to install to local Instant Wordpress:  scp -r -P 10022 wp-build/* root@localhost:/var/www/localhost/htdocs/wordpress/wp-content/plugins/outdr-widget
const PLUGIN_VERSION = '0.1.5';

const PLUGIN_SLUG = 'outdr';

function add_admin_menu() {
    add_options_page( 'Outdr Booking Widget', 'Outdr', 'activate_plugins', PLUGIN_SLUG, 'Outdr\settings_page_render');
}

function settings_init()
{
    $section = 'outdr_settings_section';
    $advanced_section = 'outdr_advanced_settings_section';

    register_setting(PLUGIN_SLUG, 'outdr_setting_school_id');
    register_setting(PLUGIN_SLUG, 'outdr_setting_activities');
    register_setting(PLUGIN_SLUG, 'outdr_setting_location_name');
    register_setting(PLUGIN_SLUG, 'outdr_setting_max_group_size', array('default' => 4));
    register_setting(PLUGIN_SLUG, 'outdr_setting_api_domain', array('default' => 'us-central1-outdr-prod.cloudfunctions.net'));
    register_setting(PLUGIN_SLUG, 'outdr_setting_highlight_colour', array('default' => '#3E01F7'));
    register_setting(PLUGIN_SLUG, 'outdr_setting_custom_css', array('default' => ''));
    register_setting(PLUGIN_SLUG, 'outdr_book_button_position', array('default' => 'bottom-right'));

    add_settings_section(
        $section,
        'Outdr Widget Settings',
        'Outdr\settings_section_cb',
        PLUGIN_SLUG
    );

    add_settings_section(
        $advanced_section,
        'Advanced Settings',
        'Outdr\advanced_settings_section_cb',
        PLUGIN_SLUG
    );
 
    add_settings_field(
        'outdr_setting_school_id',
        'School ID',
        'Outdr\setting_school_id_cb',
        PLUGIN_SLUG,
        $section
    );
    
    add_settings_field(
        'outdr_setting_location_name',
        'Location Name',
        'Outdr\setting_location_name_cb',
        PLUGIN_SLUG,
        $section
    );

    add_settings_field(
        'outdr_setting_activities',
        'Activities',
        'Outdr\setting_activities_cb',
        PLUGIN_SLUG,
        $section
    );

    add_settings_field(
        'outdr_setting_max_group_size',
        'Max Group Size',
        'Outdr\setting_max_group_size_cb',
        PLUGIN_SLUG,
        $section
    );

    add_settings_field(
        'outdr_setting_highlight_colour',
        'Highlight Colour',
        'Outdr\setting_highlight_colour_cb',
        PLUGIN_SLUG,
        $section
    );

    add_settings_field(
        'outdr_book_button_position',
        'Book Now Button Position',
        'Outdr\book_button_position_cb',
        PLUGIN_SLUG,
        $section
    );

        
    add_settings_field(
        'outdr_setting_api_domain',
        'API Domain',
        'Outdr\setting_api_domain_cb',
        PLUGIN_SLUG,
        $advanced_section
    );

    add_settings_field(
        'outdr_setting_custom_css',
        'Custom CSS Override',
        'Outdr\setting_custom_css_cb',
        PLUGIN_SLUG,
        $advanced_section
    );
}

function settings_page_render() {
    ?>
    <!-- RENDERING SETTINGS PAGE  -->
    <form method="POST" action="options.php">
        <p><img src="<?= plugins_url('outdr_logo.png', __FILE__) ?>" /></p>
        <?php
            settings_fields(PLUGIN_SLUG);
            do_settings_sections(PLUGIN_SLUG);

            submit_button();
        ?>
    </form>
    <?php
}

function settings_section_cb() {
    ?>
        <p>Configure your Outdr bookings widget below</p>
        <p>Add the widget to your page using the <i>shortcode</i> <code>[outdr]</code></p>
    <?php
}

function advanced_settings_section_cb() {
    ?>
        <p>The following settings are for advanced customization of the widget</p>
        <p>You shouldn't normally need to touch these</p>
    <?php
}

function setting_school_id_cb() {
    $value = get_option('outdr_setting_school_id');
    ?>
        <input 
            name="outdr_setting_school_id"
            id="outdr_setting_school_id" 
            type="text" 
            value="<?=$value?>" 
        />
        <label for="outdr_setting_school_id">(required)</label>
    <?php
}

function setting_activities_cb() {
    $value = get_option('outdr_setting_activities');
    ?>
        <select name="outdr_setting_activities[]" multiple>
            <?= echo_option('kitesurfing', 'kitesurfing', $value) ?>
            <?= echo_option('windsurfing', 'windsurfing', $value) ?>
            <?= echo_option('surfing', 'surfing', $value) ?>
            <?= echo_option('paddleboarding', 'paddleboarding', $value) ?>
            <?= echo_option('sailing', 'sailing', $value) ?>
            <?= echo_option('scubadiving', 'scubadiving', $value) ?>
            <?= echo_option('freediving', 'freediving', $value) ?>
            <?= echo_option('skydiving', 'skydiving', $value) ?>
            <?= echo_option('paragliding', 'paragliding', $value) ?>
            <?= echo_option('mountainbiking', 'mountainbiking', $value) ?>
            <?= echo_option('climbing', 'climbing', $value) ?>
            <?= echo_option('kayaking', 'kayaking', $value) ?>
            <?= echo_option('snowboarding', 'snowboarding', $value) ?>
            <?= echo_option('skiing', 'skiing', $value) ?>
            <?= echo_option('skitouring', 'skitouring', $value) ?>
            <?= echo_option('splitboarding', 'splitboarding', $value) ?>
        </select>
        <label for="outdr_setting_activities[]">(required, select all that apply)</label>

    <?php
}

function setting_location_name_cb() {
    $value = get_option('outdr_setting_location_name');
    ?>
        <input 
            name="outdr_setting_location_name"
            id="outdr_setting_location_name" 
            type="text" 
            value="<?=$value?>" 
        />
        <label for="outdr_setting_location_name">(required)</label>
    <?php    
}

function setting_max_group_size_cb() {
    $value = get_option('outdr_setting_max_group_size');
    echo '<input name="outdr_setting_max_group_size" id="outdr_setting_max_group_size" type="number" min="1" max="9" step="1" value="' . $value . '" />';
}

function setting_api_domain_cb() {
    $value = get_option('outdr_setting_api_domain');
    echo '<input name="outdr_setting_api_domain" id="outdr_setting_api_domain" type="text" value="' . $value . '" />';
}

function setting_highlight_colour_cb() {
    $value = get_option('outdr_setting_highlight_colour');
    echo '<input name="outdr_setting_highlight_colour" id="outdr_setting_highlight_colour" type="color" value="' . $value . '" />';
}

function setting_custom_css_cb() {
    $value = get_option('outdr_setting_custom_css');
    echo '<textarea name="outdr_setting_custom_css" id="outdr_setting_custom_css" onfocus="this.rows=25;this.cols=80;" onblur="this.rows=5;this.cols=30;" rows="5" cols="30">' . $value . '</textarea>';
}

function echo_option($value, $desc, $selected_value) {
    if (is_array($selected_value)) {
        $selected = in_array($value, $selected_value);
    } else {
        $selected = ($selected_value == $value);
    }
    ?>
        <option value="<?=$value?>"<?=$selected ? ' selected' : ''?>>
            <?=$desc?>
        </option>;
    <?php
}

function book_button_position_cb() {
    $value = get_option('outdr_book_button_position');
    echo '<select id="outdr_book_button_position" name="outdr_book_button_position">';
    echo_option('inline', 'Inline', $value);
    echo_option('top-left', 'Top Left', $value);
    echo_option('top-right', 'Top Right', $value);
    echo_option('bottom-left', 'Bottom Left', $value);
    echo_option('bottom-right', 'Bottom Right', $value);
    echo '</select>';
}

function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

function shortcode_handler($atts) {
    $schoolId = get_option('outdr_setting_school_id');
    $activities = join(',', get_option('outdr_setting_activities'));
    $locationName = get_option('outdr_setting_location_name');
    $maxGroupSize = get_option('outdr_setting_max_group_size');
    $apiDomain = get_option('outdr_setting_api_domain');
    $highlightColour = get_option('outdr_setting_highlight_colour');
    $styleSheetPath = plugins_url('static/css/main.css', __FILE__);
    $customCss = encodeURIComponent(get_option('outdr_setting_custom_css'));
    $buttonPosition = get_option('outdr_book_button_position');

    return <<<HTML
        <div 
            class="outdr-widget"
            data-stylesheet-path="$styleSheetPath"
            data-activities="$activities" 
            data-location-name="$locationName" 
            data-max-group-size="$maxGroupSize"
            data-school-id="$schoolId"
            data-api-domain="$apiDomain"
            data-highlight-colour="$highlightColour"
            data-custom-css="$customCss"
            data-button-position="$buttonPosition">
        </div>
HTML;
}

function include_react_files() {
    wp_enqueue_script('outdr-main-js', plugins_url('static/js/main.js', __FILE__), array(), PLUGIN_VERSION, true);
}

function defer_script_loader_tag($tag, $handle) {
	if ($handle === 'outdr-main-js') {
		if (false === stripos($tag, 'defer')) {
			$tag = str_replace('<script ', '<script defer ', $tag);
		}
	}
	return $tag;	
}


add_action('wp_enqueue_scripts', 'Outdr\include_react_files');
add_filter('script_loader_tag', 'Outdr\defer_script_loader_tag', 10, 2);
add_action('admin_init', 'Outdr\settings_init');
add_action('admin_menu', 'Outdr\add_admin_menu' );
add_shortcode('outdr', 'Outdr\shortcode_handler');