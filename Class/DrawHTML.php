<?php

/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 02/12/15
 * Time: 10:17
 */
class DrawHTML
{
    private $plugin_name;

    function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }

    public function postbox($title,$step,$ratings,$globalCount)
    {
        switch($step)
        {
            case 0:
                $icon = "matiks_wot_very_poor";
                break;
            case 1:
                $icon = "matiks_wot_poor";
                break;
            case 2:
                $icon = "matiks_wot_unsatisfactory";
                break;
            case 3:

                $icon = "matiks_wot_unknown";
                break;
            case 4:
                $icon = "matiks_wot_good";
                break;
            case 5:
                $icon = "matiks_wot_excellent";
                break;
            default:
                $icon ="";
        }
        if( count($ratings) <= 0 )
            $icon .= " mw_disabled";


        $HTML  =  "<p class='pointer'><span class='dashicons dashicons-controls-play'></span></p><h1 class='". $icon."'><a mw_link='active' href='#'>".$title." <span class='dashicons dashicons-arrow-right-alt'></span> ";

        if( count($ratings) > 0 )
            $HTML .=   sprintf(  _n('<b>%d</b> link was found!</a></h1>', '<b>%d</b> links were found!</a></h1>', count($ratings) , $this->plugin_name), count($ratings) );
        else
            $HTML .=   sprintf( __('no link was found!</a></h1>',$this->plugin_name));

        $HTML .= '<table id="table_responsive_'.$step.'" class="matiks_net_table_result">';
        $HTML .= '<thead>
                    <tr>
                        <th>'.  sprintf( __('Author', $this->plugin_name)).'</th>
                        <th>'.  sprintf( __('Post title', $this->plugin_name)).'</th>
                        <th>'.  sprintf( __('Comment', $this->plugin_name)).'</th>
                        <th>'.  sprintf( __('Rating', $this->plugin_name)).'</th>
                        <th>'.  sprintf( __('Action', $this->plugin_name)).'</th>
                        <th>'.  sprintf( __('URL', $this->plugin_name)).'</th>
                        <th>'.  sprintf( __('LINK', $this->plugin_name)).'</th>
                    </tr>
                </thead>
                <tbody>';
        $HTML .= $this->postboxElements($ratings,$step);
        $HTML .=  '</tbody></table>';


        $response['html'] = $HTML;
        $response['count'] = count($ratings);
        $response['globalCount'] = $globalCount;

        if( is_numeric($globalCount) && $globalCount < 1 )
            $response['noresult'] = '<center><div class="mw_alert mw_alert-danger text-center width60">'. sprintf( __("No domain to analyze with the criteria!", $this->plugin_name)).'</div></center>';

        return $response;
    }




    private function postboxElements($ratings,$step)
    {
        $HTML = "";
        foreach($ratings as $result)
        {
            $HTML .= "<tr>";
            $HTML .= "<td>$result->author</td>";
            $HTML .= "<td>$result->title</td>"; //Display related post title
            $HTML .= "<td>".(is_numeric($result->comment_id)? sprintf( __('YES', $this->plugin_name)): sprintf( __('NO', $this->plugin_name)))."</td>"; //Is a comment
            $HTML .= "<td>".(($result->trust>=0)?$result->trust." / 100": sprintf( __('unknown', $this->plugin_name)))."</td>";
            $HTML .= "<td>".((is_numeric($result->comment_id) && $step < 3 )?"<button comment_id='".$result->comment_id."' class='button-small mw_btn_disapproved_comment button button-primary'>". sprintf( __('Disapprove', $this->plugin_name))."</button>":'')."</td>";
            $HTML .= "<td>$result->url</td>";
            $HTML .= "<td style='cursor:pointer;' onclick='window.open(\"".$result->link."\", \"_blank\")'><span class='dashicons dashicons-admin-links'></span></td>";
            $HTML .= "</tr>";
        }

        return $HTML;
    }
}