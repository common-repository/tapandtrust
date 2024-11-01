<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 */
?>



<?php
$active_tab = "comments";
if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = $_GET[ 'tab' ];
} // end if
?>

<h2 class="nav-tab-wrapper">
    <a href="?page=<?php echo $this->plugin_name;?>" class="nav-tab <?php echo $active_tab == 'comments' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('Settings', $this->plugin_name);?></a>
    <a href="?page=<?php echo $this->plugin_name;?>validate" class="nav-tab <?php echo $active_tab == 'validate' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('Website validation', $this->plugin_name);?></a>
    <a href="?page=<?php echo $this->plugin_name;?>analysis" class="nav-tab <?php echo $active_tab == 'analysis' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('Analysis', $this->plugin_name);?></a>
    <a href="?page=<?php echo $this->plugin_name;?>about" class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('About', $this->plugin_name);?></a>
</h2>

<?php
//Grab all options
$options = get_option($this->plugin_name);
$api_key = isset($options['api_key'])?$options['api_key']:"";
function _is_curl_installed() {
    if  (in_array  ('curl', get_loaded_extensions())) {
        return true;
    }
    else {
        return false;
    }
}
if (!_is_curl_installed() ) { ?>
<div class="error notice" ><p><?php  _e('Error: cURL is NOT installed on this server. <a href="http://php.net/manual/install.unix.debian.php" target="_blank">Install cURL</a>', $this->plugin_name);?></p></div>
<?php
}
?>
<?php
if (!preg_match('/^[a-f0-9]{40}$/', $api_key) ) { ?>
<div class="update-nag notice"><p><?php _e('You need <a href="https://www.mywot.com/en/signup" target="_blank">an account</a> at WOT and to <a href="https://www.mywot.com/profile/api" target="_blank">request your API key</a> to make this plugin works', $this->plugin_name);?></p></div>
<?php
}
?>
<div class="wrap">

    <form method="post" name="matiks_mywot_options" class="mw_highlight" action="options.php">


        <?php if( isset($_GET['settings-updated']) ) { ?>
            <div id="message" class="updated">
                <p><strong><?php _e('Settings saved.') ?></strong></p>
            </div>
        <?php } ?>

        <?php

        if ($active_tab == "comments") {
            include_once('inc/comments.inc.php');
         }elseif ($active_tab == "validate") {
            include_once('inc/validate.inc.php');
        } elseif ($active_tab == "analysis") {
            include_once('inc/analysis.inc.php');
        }elseif ($active_tab == "about") {
            include_once('inc/about.inc.php');
        }?>

    </form>


</div>