<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 31/12/15
 * Time: 11:09
 */
defined('ABSPATH') or die("No script kiddies please!");

?>

    <h2 class="nav-tab-wrapper"><?php _e('About', $this->plugin_name);?></h2>

    <p style="font-size: 1.2em;margin-top: 30px;margin-left: 5px;text-indent: 20px;text-align: justify;">
        <?php _e('Tap&Trust helps you to protect your visitors by blocking unsafe links in comments. Thresholds can be customized in the settings page. This plugin also helps you to analyze your site to find malicious links and to disapprove them. A last tool allows you to easily validate your site at WOT.', $this->plugin_name);?>
    </p>
    <p>
        <img src="../wp-content/plugins/<?php echo $this->repinstall;?>/admin/img/tapntrust_icon.png" style="float: left;margin-right: 10px;">
        <div style="padding-top: 5px;font-weight: bold;font-size: 1.2em;">
            <?php _e('Tap&Trust for WordPress is not an official application of <a href="https://www.mywot.com" target="_blank">Web Of Trust</a> - MyWOT', $this->plugin_name);?>
        </div>
    </p>
    <p style="color: darkred;font-size: 1.2em;margin-top: 30px;margin-left: 5px;">
        <?php _e('<span class="dashicons dashicons-warning"></span> This plugin needs a valid API key to work properly. You can <a href="https://www.mywot.com/user/api" target="_blank">request yours</a> for free once you have created  <a href="https://www.mywot.com/en/signup" target="_blank">an account</a> at WOT', $this->plugin_name);?>
    </p>

    <p style="font-size: 1.2em;margin-top: 30px;margin-left: 5px;">
        <?php _e('<span class="dashicons dashicons-email"></span> If you have any questions, please contact me at matiks.net@gmail.com', $this->plugin_name);?>
    </p>