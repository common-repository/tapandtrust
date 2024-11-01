<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 08/11/15
 * Time: 16:33
 */
defined('ABSPATH') or die("No script kiddies please!");


?>
    <!-- Login page customizations -->

    <h2 class="nav-tab-wrapper"><?php _e('Analysis of pages, posts and comments', $this->plugin_name);?></h2>

    <p><?php _e('Include or exclude posts or comments from the analysis', $this->plugin_name);?></p>

    <form id="matiks_wot_analysis_form" >
        <label for="matiks_wot_analysis_pages" >
            <span><?php esc_attr_e('Analyze pages ?', $this->plugin_name);?></span>
            <input type="checkbox" id="matiks_wot_analysis_pages" checked />
        </label>
        <br/><br/>
        <label for="matiks_wot_analysis_posts" >
            <span><?php esc_attr_e('Analyze posts ?', $this->plugin_name);?></span>
            <input type="checkbox" id="matiks_wot_analysis_posts" checked />
        </label>
        <br/><br/>
        <label for="matiks_wot_analysis_comments" >
            <span><?php esc_attr_e('Analyze comments ?', $this->plugin_name);?></span>
            <input type="checkbox" id="matiks_wot_analysis_comments" checked />
        </label>

    </form>
    <button id="matiks_wot_analysis" class="button button-primary">Analyze</button>

<div  class="hidden mw_alert mw_alert-danger center text-error" id="analysis_no_selection" style="margin-top:20px;" ><?php esc_attr_e('Please, select at least one item!', $this->plugin_name);?> </div>

    <div class="mw_highlight hidden" id="analyze_container">
        <div id="analysis" >
            <div  class="hidden mw_line_result" id="analysis_p" ><?php esc_attr_e('Retrieving URL in messages, please wait', $this->plugin_name);?> <span class="loader-green-h">&nbsp</span></div>
            <div  class="hidden mw_line_result" id="analysis_p_done"></div>
            <br/>
            <div id="analysis_p_domains" class="hidden mw_line_result"><?php esc_attr_e('Extraction of domains, please wait', $this->plugin_name);?> <span class="loader-green-h">&nbsp</span></div>
            <div id="analysis_p_domains_done" class="hidden mw_line_result"></div>
        </div>
        <div class="hidden" id="error">
            <br/><br/>
            <div class="mw_alert mw_alert-danger center text-error" id="error_message"></div>
        </div>

        <div id="domains_analysis" class="hidden" >
            <div class="demo-wrapper html5-progress-bar">
                <div class="progress-bar-wrapper">
                    <progress id="progressbar" value="0" max="100"></progress>
                    <span class="progress-value">0%</span>
                </div>
            </div>
        </div>

    </div>


    <div id="wot_last_scan" class="hidden mw_highlight ">
        <div id="scan_loader" style="text-align: center;" class="hidden"><span class="loader-green-sq"></span> </div>
        <div id="result" class="hidden">
            <h2 id="last_analyze" class="nav-tab-wrapper"></h2>
            <div id="no_result_aera"></div>
            <div id="sections_aera">
                <section id="mw_vp_results" class="ac_hidden"></section>
                <section id="mw_p_results" class="ac_hidden"></section>
                <section id="mw_u_results" class="ac_hidden"></section>
                <section id="mw_un_results" class="ac_hidden"></section>
                <section id="mw_g_results" class="ac_hidden"></section>
                <section id="mw_e_results" class="ac_hidden"></section>
            </div>
        </div>
    </div>

    <span id="matiks_net_data_trans"
          data-disapproved="<?php esc_attr_e('disapproved', $this->plugin_name);?>"
          data-error="<?php esc_attr_e('error', $this->plugin_name);?>"
        ></span>




