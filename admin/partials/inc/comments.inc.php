<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 08/11/15
 * Time: 16:33
 */
defined('ABSPATH') or die("No script kiddies please!");


// Cleanup

$min_trust = isset($options['min_trust'])?$options['min_trust']:60;
$min_child = isset($options['min_child'])?$options['min_child']:60;
$unknown = isset($options['unknown'])?$options['unknown']:0;
$block = isset($options['block'])?$options['block']:0;


settings_fields($this->plugin_name);

?>
    <!-- Login page customizations -->

    <h2 class="nav-tab-wrapper"><?php _e('Settings - Safety lock links in comments', $this->plugin_name);?></h2>

    <p><?php _e('Customize the threshold to lock links in comments', $this->plugin_name);?></p>

    <!-- add your logo to login -->
    <fieldset>
        <legend class="screen-reader-text">
            <span><?php esc_attr_e('MyWOT detection:', $this->plugin_name);?></span>
        </legend>
        <div id="mw_slider_container">

            <div id="slider_trust" style="position: relative;float: left;margin: 20px;" ></div>
            <div id="slider_child"  style="position: relative;float: left;margin: 20px;"></div>
        </div>
        <input type="hidden" min="0" max="100" id="<?php echo $this->plugin_name;?>-min_trust" name="<?php echo $this->plugin_name;?>[min_trust]" value="<?php echo $min_trust; ?>" />
        <input type="hidden" min="0" max="100" id="<?php echo $this->plugin_name;?>-min_child" name="<?php echo $this->plugin_name;?>[min_child]" value="<?php echo $min_child; ?>" />

        <div id="mw_slider_container" style="position: relative;float: left;margin: 20px;margin-top: 50px;">
            <label for="<?php echo $this->plugin_name;?>-block" >
                <span><?php esc_attr_e('Lock all links before to know their rating with WOT', $this->plugin_name);?></span>
                <input type="checkbox" id="<?php echo $this->plugin_name;?>-block" name="<?php echo $this->plugin_name;?>[block]" <?php if($block){echo "checked";}?> value="1" />
            </label>
            <br/><br/>
            <label for="<?php echo $this->plugin_name;?>-unknown">
                <span><?php esc_attr_e('Lock links having an unknown rating', $this->plugin_name);?>&nbsp;&nbsp;</span>
                <input type="checkbox" id="<?php echo $this->plugin_name;?>-unknown" name="<?php echo $this->plugin_name;?>[unknown]"  <?php if($unknown){echo "checked";}?> value="1" />

            </label>
            <br/><br/>
            <?php
            if( !preg_match('/^[a-f0-9]{40}$/', $api_key) )
            {
                ?>
                <div style="color:red;margin-top: 10px;margin-bottom: 10px;font-weight: bold;font-style: italic;">
                    <?php esc_attr_e('You need a valid API key to make this plugin work! This is free.', $this->plugin_name);?>
                </div>

                <?php
            }
            ?>
            <label for="<?php echo $this->plugin_name;?>-api_key">
                <span><?php esc_attr_e('Your WOT API key ', $this->plugin_name);?>&nbsp;&nbsp;</span>
                <input maxlength="40" size="40" type="text" id="<?php echo $this->plugin_name;?>-api_key" name="<?php echo $this->plugin_name;?>[api_key]" value="<?php echo $api_key; ?>" />
            </label>
        </div>
    </fieldset>

<?php submit_button(__('Save', $this->plugin_name), 'primary','submit', TRUE); ?>