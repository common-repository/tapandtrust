<?php

/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 01/11/15
 * Time: 09:52
 */
defined('ABSPATH') or die("No script kiddies please!");
class Tools
{


    public function detectLinks($comment)
    {

        preg_match_all('/<a\s[^>]*href=([\"\']??)([^\\1 >]*?)\\1[^>]*>(.*)<\/a>/siU', $comment, $result_a);
        preg_match_all('/\[url=([\"\']??)([^\\1 \]]*?)\\1[^\]]*\](.*)\[\/url\]/siU', $comment, $result_b);

        $result[0] = array_merge($result_a[0], $result_b[0]);
        $result[1] = array_merge($result_a[1], $result_b[1]);
        $result[2] = array_merge($result_a[2], $result_b[2]);
        $result[3] = array_merge($result_a[3], $result_b[3]);

        return $result;
    }

    /**
     * Disable links if needed and adds an attribute mw_ratings with the domain as value: mw_ratings="example.com"
     * @param $comment
     * @param $block
     * @return mixed
     */
    public function disableLinks($comment, $block)
    {

        if( $block )
        {
            return preg_replace_callback ("/<a\s(.+?)>(.+?)<\/a>/is",
                function ($matches) {
                    preg_match_all('/<a\s[^>]*href=([\"\']??)([^\\1 >]*?)\\1[^>]*>(.*)<\/a>/siU', $matches[0], $result_a);

                    return "<span ".$matches[1]." mw_ratings='".$this->get_domain_fast($result_a[2][0])."' >".$matches[2]."</span>";
                }, $comment);

        }
        else
        {
            return preg_replace_callback ("/<a\s(.+?)>(.+?)<\/a>/is",
                function ($matches) {
                    preg_match_all('/<a\s[^>]*href=([\"\']??)([^\\1 >]*?)\\1[^>]*>(.*)<\/a>/siU', $matches[0], $result_a);
                    return "<a ".$matches[1]." mw_ratings='".$this->get_domain_fast($result_a[2][0])."' >".$matches[2]."</a>";
                }, $comment);
        }
    }


    /**
     * Returns all domains in comments for an article
     * @param $post_id
     * @return array
     */
    public function get_all_domain($post_id)
    {
        $args = array('post_id' => $post_id, 'status' => 'approve');
        $comments = get_comments($args);

        $uniq_domain = array();
        foreach($comments as $comment)
        {
            //Retrieves all links in comment
            $results_tmp = $this->detectLinks($comment->comment_content);
            foreach($results_tmp[2] as $link )
            {
                $domain = $this->get_domain_fast($link);

                if( ! in_array($domain,$uniq_domain))
                {
                    $uniq_domain[] = $domain;
                }
            }
            //If there is a domain url associated to screenName
            $domain = $this->get_domain_fast($comment->comment_author_url);

            if( ! in_array($domain,$uniq_domain) && $domain != "http://")
            {
                $uniq_domain[] = $domain;
            }
        }
        return $uniq_domain;
    }


    /**
     * Put approved comment to false
     * @param $comment_id
     * @return mixed
     */
    public function disapproved_comment($comment_id)
    {
        global $wpdb;
        return $wpdb->update(
            $wpdb->prefix .'comments',
            array(
                'comment_approved' => 0,
            ),
            array( 'comment_ID' => $comment_id)
        );
    }

    /**
     * returns the domain from an url
     * @param $url
     * @return bool
     */
    public function get_domain_fast($url)
    {
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'http://' . ltrim($url, '/');
        }
        $host = (parse_url($url, PHP_URL_HOST) != '') ? parse_url($url, PHP_URL_HOST) : $url;
        return preg_replace('/^www\./', '', $host);
    }


    /**
     * Returns value in cache if not too old, also delete old ratings.
     * If no rating available => $result['valid'] = false;
     * @param $domain
     * @return array
     */
    public function ratingInCache($domain)
    {
        global $wpdb;
        //Check if rating exists in db
        $results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . 'matiks_wot_ratings'." WHERE domain = '".$domain."'", OBJECT );
        $result = array();

        if( !empty($results) )
        {
            $date = new \DateTime();
            $date = $date->sub(new \DateInterval('PT60M'));
            $cachedDate = new DateTime( $results[0]->date );
            //Cache is not too old?
            if( $date >  $cachedDate )
            {
                //Yes. Must be updated
                $result['valid'] = false;
                //Delete the old rating
                $wpdb->delete( $wpdb->prefix . 'matiks_wot_ratings', array( 'domain' => $domain  ) );
            }
            else
            {
                $result['valid'] = true;
                $result['domain'] = $results[0]->domain;
                $result['trust'] = $results[0]->trust;
                $result['trust_c'] = $results[0]->trust_c;
                $result['child'] = $results[0]->child;
                $result['child_c'] = $results[0]->child_c;
                $result['items'] = unserialize($results[0]->items);
                $result['blacklist'] = unserialize($results[0]->blacklist);
            }
        }
        else
        {
            $result['valid'] = false;
        }
        return $result;
    }

    /**
     * Returns WOT ratings for analyzed domains and cache them
     * $offset <= 100
     * $domains_to_check can be greater than 100, the array will be split by the offset value and step value
     * @param $domains_to_check
     * @param $offset
     * @param $step
     * @param $api_key
     * @return array
     */
    public function wot_get_ratings($domains_to_check,$offset,$step,$api_key)
    {
        $MyWOTResults = $domains_array = array();

        $domains_to_check_tmp = array_slice($domains_to_check, $offset, $step);
        $domainsList = "";
        foreach($domains_to_check_tmp as $d_c)
        {
            $domainsList .= $d_c."/";
        }


        if( !preg_match('/^[a-f0-9]{40}$/', $api_key))
        {
            return array('error' => 'invalide API KEY');
        }

        if( trim($domainsList) != "") {
            $url_wot = "https://api.mywot.com/0.4/public_link_json2?hosts=" . $domainsList . "/&key=" . $api_key;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url_wot);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURL_HTTP_VERSION_1_1, true);
            curl_setopt($curl, CURLOPT_ENCODING, "gzip, deflate");
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3");
            //To avoid error 60, queries are only done at WOT.
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $MyWOTResults = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if($httpCode == 403) {
                return array('error' => 'Forbidden');
            }
            if (curl_errno($curl)) {
                return array('error' => 'Curl error: ' . curl_errno($curl));
            }
            curl_close($curl);
            $MyWOTResults = json_decode($MyWOTResults);
        }
        if (isset($MyWOTResults) && !empty($MyWOTResults)) {
            global $wpdb;
            $analyze = explode("/", $domainsList);
            $dateInsert = new DateTime();
            foreach ($analyze as $domain) {

                if( isset($MyWOTResults->{$domain}) )
                {
                    $result = $MyWOTResults->{$domain};

                    if (!isset($result->{4}[0]))
                        $result->{4}[0] = -1;
                    if (!isset($result->{4}[1]))
                        $result->{4}[1] = -1;
                    if (!isset($result->{0}[0]))
                        $result->{0}[0] = -1;
                    if (!isset($result->{0}[1]))
                        $result->{0}[1] = -1;
                    $categories = $blacklists = array();
                    if( isset( $result->categories  ) )
                    {
                        foreach($result->categories as $category => $val)
                        {
                            $categories[] = $category;
                        }
                    }
                    if( isset( $result->blacklists  ) ) {
                        foreach ($result->blacklists as $list => $val) {
                            $blacklists[] = $list;
                        }
                    }
                    //Cache WOT ratings in db
                    $domains_array[$domain]['trust'] = $result->{0}[0];
                    $domains_array[$domain]['child'] = $result->{4}[0];
                    $domains_array[$domain]['trust_c'] = $result->{0}[1];
                    $domains_array[$domain]['child_c'] = $result->{4}[1];
                    $wpdb->insert(
                        $wpdb->prefix . 'matiks_wot_ratings',
                        array(
                            'domain' => $domain,
                            'trust' => $result->{0}[0],
                            'trust_c' => $result->{0}[1],
                            'child' => $result->{4}[0],
                            'child_c' => $result->{4}[1],
                            'date' => $dateInsert->format('Y-m-d H:i:s'),
                            'items' => serialize($categories),
                            'blacklist' => serialize($blacklists)
                        ));
                }

            }
        }
        return $domains_array;
    }
}