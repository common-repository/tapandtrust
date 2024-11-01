<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 08/11/15
 * Time: 16:33
 */
defined('ABSPATH') or die("No script kiddies please!");


//Grab all options
$options = get_option($this->plugin_name.'validate');
// Cleanup
$validation_code = isset($options['validation_code'])?$options['validation_code']:"";

settings_fields($this->plugin_name.'validate');?>
    <h2 class="nav-tab-wrapper"><?php _e('Website verification', $this->plugin_name);?></h2>

    <p><?php _e('Please, enter your verification code', $this->plugin_name);?></p>
    <fieldset>
        <legend class="screen-reader-text">
            <span><?php esc_attr_e('Please, enter your verification code', $this->plugin_name);?></span>
        </legend>

        <label for="<?php echo $this->plugin_name;?>-validation_code">
            <span><?php esc_attr_e('Code ', $this->plugin_name);?>&nbsp;&nbsp;</span>
            <input maxlength="20" size="20" type="text" id="<?php echo $this->plugin_name;?>-validation_code" name="<?php echo $this->plugin_name;?>validate[validation_code]" value="<?php echo $validation_code; ?>" />
            <?php
            if( !preg_match('/^[a-f0-9]{20}$/', $validation_code) ) {
                ?>
                <br/><br/>
                <ul>
                <li>
                    <?php _e('You need <a href="https://www.mywot.com/en/signup" target="_blank">an account</a> at WOT and to <a href="https://www.mywot.com/profile/mysites" target="_blank">request your site</a>', $this->plugin_name); ?>
                </li>
                <li>
                    <?php _e('Click on "<strong>[verify]</strong>"', $this->plugin_name); ?>
                </li>
                <li>
                    <?php _e('Copy/past the code similar to this in red: -<i><strong>&lt;meta name="wot-verification" content="<em style="color:red;">abcdefabc0123456789</em></strong></i>"/>', $this->plugin_name); ?>
                </li>
                </ul>
                <?php
            }
            else
            {
                ?>
                <br/><br/>
                <?php _e('The meta tag: <strong>&lt;meta name="wot-verification" content="'.$validation_code.'"/></strong> was added to the header of your site', $this->plugin_name); ?>
                <br/>
                <?php _e('To remove it, let the field above blank and click on <strong>Save</strong>', $this->plugin_name); ?>
                <?php
            }
            ?>

        </label>
    </fieldset>
<?php submit_button(__('Save', $this->plugin_name), 'primary','submit', TRUE); ?>