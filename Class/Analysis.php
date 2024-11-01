<?php

/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 08/11/15
 * Time: 18:00
 */
defined('ABSPATH') or die("No script kiddies please!");

class Analysis
{


    private $plugin_name;


    function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }

    /**
     * Returns numbers of posts or comments which will be analyzed
     * @return string
     */
    public function getInfo($withPages = true, $withPosts = true, $withComments = true)
    {
        global $wpdb;
        $wpdb->query('DELETE FROM '. $wpdb->prefix . 'matiks_wot_scan_results');
        $wpdb->query('DELETE FROM '. $wpdb->prefix . 'matiks_wot_scans' );
        $dateInsert = new DateTime();
        $response = array();

        $response['countPages'] = ( $withPages == "true" )?wp_count_posts('page')->publish:0;
        $response['countPosts'] = ( $withPosts == "true" )?wp_count_posts()->publish:0;
        $response['countComments'] = ( $withComments == "true")? wp_count_comments()->approved:0;

        $response['message']  = sprintf( esc_attr( _n('%d page, ', '%d pages, ', $response['countPages'] , $this->plugin_name)), $response['countPages'] );
        $response['message'] .= sprintf( esc_attr( _n('%d post and ', '%d posts and ', $response['countPosts'] , $this->plugin_name)), $response['countPosts'] );
        $response['message'] .= sprintf( esc_attr( _n('%d comment will be analyzed.', '%d comments will be analyzed.', $response['countComments'] , $this->plugin_name)), $response['countComments'] );
        //Insert the current scan
        $wpdb->insert(
            $wpdb->prefix . 'matiks_wot_scans',
            array(
                'date' => $dateInsert->format('Y-m-d H:i:s'),
                'user_id' => get_current_user_id(),
                'with_posts' => ( $withPosts == "true" )?1:0,
                'with_pages' => ( $withPages == "true" )?1:0,
                'with_comments' =>   ( $withComments == "true")?1:0,
                'countPages' => wp_count_posts('page')->publish,
                'countPosts' => wp_count_posts()->publish,
                'countComments' => wp_count_comments()->approved
            )
        );
        //response will be used to display some data via javascript
        return $response;

    }

    /**
     * Returns the last scan values from DB
     * @return array
     */
    private function get_current_scan()
    {
        global $wpdb;
        $current_scan = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . 'matiks_wot_scans'." WHERE user_id = '".get_current_user_id()."' ORDER BY date DESC LIMIT 1", OBJECT );
        $scan = array();
        if( !empty($current_scan) )
        {
            $scan['id'] = $current_scan[0]->id;
            $scan['date'] = $current_scan[0]->date;
            $scan['user_id'] = $current_scan[0]->user_id;
            $scan['with_posts'] = $current_scan[0]->with_posts;
            $scan['with_pages'] = $current_scan[0]->with_pages;
            $scan['with_comments'] = $current_scan[0]->with_comments;
            $scan['countPosts'] = $current_scan[0]->countPosts;
            $scan['countComments'] = $current_scan[0]->countComments;
        }
        return $scan;
    }

    /**
     * Returns all distinct domains from a scan
     * @param $scan_id
     * @return mixed
     */
    private function getUniqDomainsFromAnalyze($scan_id)
    {
        global $wpdb;
        return $wpdb->get_results("SELECT domain FROM ".$wpdb->prefix . 'matiks_wot_scan_results'." WHERE ".$wpdb->prefix ."matiks_wot_scans_id = '".$scan_id."' GROUP BY domain", OBJECT );
    }


    /**
     * Returns a scan of different posts & comments
     * @return array
     */
    public function getURLs()
    {
        global $wpdb;
        $current_scan = $this->get_current_scan();
        $withPosts = $current_scan['with_posts'];
        $withPages = $current_scan['with_pages'];
        $withComments = $current_scan['with_comments'];
        $response = array();

        @define('ROOTPATH', __DIR__);

        require_once(ROOTPATH.'/../Class/Tools.php');
        $Tools = new Tools();
        if( $withPages )
        {
            $ALL_PAGES= get_pages(array('post_status' => 'publish'));
            if( count($ALL_PAGES) > 0 )
            {
                foreach($ALL_PAGES as $_p)
                {
                    $results_tmp = $Tools->detectLinks( $_p->post_content );
                    foreach($results_tmp[2] as $link )
                    {
                        $domain = $Tools->get_domain_fast($link);
                        //Links to the website scanned are excluded
                        if( $domain != php_uname('n') && $domain != "http://" && $domain != $_SERVER['SERVER_NAME'])
                        {
                            $uniq_domain[] = $domain;
                            $wpdb->insert(
                                $wpdb->prefix . 'matiks_wot_scan_results',
                                array(
                                    'domain' => $domain,
                                    'url' => $link,
                                    'link' => $_p->guid,
                                    'author' => get_the_author_meta( 'display_name', $_p->post_author ),
                                    'author_id' => $_p->post_author,
                                    'post_id' => $_p->ID,
                                    'title' => $_p->post_title,
                                    $wpdb->prefix .'matiks_wot_scans_id' => $current_scan['id']
                                )
                            );
                        }
                    }
                }
            }

        }

        if( $withPosts )
        {
            $ALL_POSTS = get_posts(array('post_status' => 'publish'));
            if( count($ALL_POSTS) > 0 )
            {
                foreach($ALL_POSTS as $_p)
                {
                    $results_tmp = $Tools->detectLinks( $_p->post_content );
                    foreach($results_tmp[2] as $link )
                    {
                        $domain = $Tools->get_domain_fast($link);
                        //Links to the website scanned are excluded
                        if( $domain != php_uname('n') && $domain != "http://" && $domain != $_SERVER['SERVER_NAME'])
                        {
                            $uniq_domain[] = $domain;
                            $wpdb->insert(
                                $wpdb->prefix . 'matiks_wot_scan_results',
                                array(
                                    'domain' => $domain,
                                    'url' => $link,
                                    'link' => $_p->guid,
                                    'author' => get_the_author_meta( 'display_name', $_p->post_author ),
                                    'author_id' => $_p->post_author,
                                    'post_id' => $_p->ID,
                                    'title' => $_p->post_title,
                                    $wpdb->prefix .'matiks_wot_scans_id' => $current_scan['id']
                                )
                            );
                        }
                    }
                }
            }

        }
        if( $withComments )
        {
            $ALL_COMMENTS = get_comments(array('status' => "approve"));
            if( count($ALL_COMMENTS) > 0 )
            {
                foreach($ALL_COMMENTS as $_c)
                {
                    $results_tmp = $Tools->detectLinks( $_c->comment_content );
                    //Add the comment author url
                    $results_tmp[2][] = $_c->comment_author_url;
                    foreach($results_tmp[2] as $link )
                    {
                        $domain = $Tools->get_domain_fast($link);
                        //Links to the website scanned are excluded
                        if( $domain != php_uname('n') && $domain != "http://"  && $domain != $_SERVER['SERVER_NAME']) {
                            $domain = $Tools->get_domain_fast($link);
                            $wpdb->insert(
                                $wpdb->prefix . 'matiks_wot_scan_results',
                                array(
                                    'domain' => $domain,
                                    'url' => $link,
                                    'link' => get_comment_link($_c->comment_ID),
                                    'author' => $_c->comment_author,
                                    'author_id' => $_c->user_id,
                                    'post_id' => $_c->comment_post_ID,
                                    'comment_id' => $_c->comment_ID,
                                    'title' => get_the_title($_c->comment_post_ID),
                                    $wpdb->prefix .'matiks_wot_scans_id' => $current_scan['id']
                                )
                            );
                        }
                    }
                }
            }
        }
        $count = count($this->getUniqDomainsFromAnalyze($current_scan['id']));
        $response['analyzedDomainsText'] = sprintf( __( _n('%d domain to analyze', '%d domains to analyze', $count , $this->plugin_name)), $count );
        $response['analyzedDomains'] =  $count;
        return $response;
    }


    /**
     * Analyzes domains by bulk of 100
     * Results come from DB or from MyWOT API if not cached
     * @param $api_key
     * @return array
     */
    public function analyzeDomains($inc, $step, $api_key)
    {

        if(  !preg_match('/^[a-f0-9]{40}$/', $api_key ) )
        {
            $response['error'] = sprintf( __('The API key does not seem to be valid!', $this->plugin_name));
            return $response;
        }
        global $wpdb;
        $current_scan = $this->get_current_scan();


        @define('ROOTPATH', __DIR__);
        require_once(ROOTPATH.'/../Class/Tools.php');


        $Tools = new Tools();
        $all_domains = $domains_to_check = array();
        foreach($this->getUniqDomainsFromAnalyze($current_scan['id']) as $result)
        {
            $all_domains[] = $result->domain;
        }

        $domains_array_slice = array_slice($all_domains,$inc*$step,$step);
        //Get domains with valid ratings in cache
        foreach($domains_array_slice as $domain)
        {
            $rating = $Tools->ratingInCache($domain);
            //Domain is not valid and is added to be checked with the WOT API
            if( !$rating['valid']) //Will need to be enqueue for a WOT query
            {
                $domains_to_check[] = $domain;
            }
            else //If valid, values are taken
            {
                $domains_valid[] = $domain;
                $domains_array[$domain]['trust'] = $rating['trust'];
                $domains_array[$domain]['child'] = $rating['child'];
                $domains_array[$domain]['trust_c'] = $rating['trust_c'];
                $domains_array[$domain]['child_c'] = $rating['child_c'];
            }
        }
        //3 tries max with  curl call
        $number_of_tries = 0;

        //An error occurred?
        $succes = false;

        //
        while( !$succes && $number_of_tries <= 3 )
        {
            $domains_array_tmp = $Tools->wot_get_ratings($domains_to_check,0,count($domains_to_check),$api_key);
            if( isset($domains_array_tmp['error']) )
            {
                $number_of_tries++; //increases tries
                if( $number_of_tries >= 3 )
                {
                    $response['error'] =  sprintf( __('Something went wrong with the WOT API!', $this->plugin_name));
                    return $response;
                }
            }
            else
            {
                $succes = true; //No problem occurred, var $number_of_tries is reinitialized to zero
            }
            //100 ms of break
            usleep(100000);
        }
        //Merge domains with ratings
        if( isset($domains_array_tmp) && isset($domains_array))
            $analyzed_domains = array_merge($domains_array,$domains_array_tmp);
        else if (!isset($domains_array_tmp) && isset($domains_array))
            $analyzed_domains = $domains_array;
        else if (isset($domains_array_tmp) && !isset($domains_array))
            $analyzed_domains = $domains_array_tmp;
        else
            $analyzed_domains = array();
        //Update scan values with WOT Ratings
        foreach($analyzed_domains as  $domain => $values)
        {

            $wpdb->update(
                $wpdb->prefix .'matiks_wot_scan_results',
                array(
                    'trust' => $values['trust'],
                    'trust_c' => $values['trust_c'],
                    'child' => $values['child'],
                    'child_c' => $values['child_c']
                ),
                array( $wpdb->prefix .'matiks_wot_scans_id' => $current_scan['id'], 'domain' => $domain)
            );
        }
        return $response['done'] = true;
    }

    /**
     * Disapproved comments & removed result in analysis
     * @param $comment_id
     * @return mixed
     */
    public function disapprovedComment($comment_id)
    {
        global $wpdb;
        @define('ROOTPATH', __DIR__);
        require_once(ROOTPATH.'/../Class/Tools.php');
        $Tools = new Tools();
        $Tools->disapproved_comment($comment_id);
        return $wpdb->delete( $wpdb->prefix . 'matiks_wot_scan_results', array( 'comment_id' => $comment_id ) );
    }

    /**
     * Returns the number of domains which will be analyzed
     * @return int
     */
    private function globalCount()
    {
        $globalCount = 0;
        $current_scan = $this->get_current_scan();
        if( isset($current_scan['countPosts']) && isset($current_scan['countComments']))
        {
            $globalCount =  ($current_scan['countPosts'] + $current_scan['countComments']);
        }
        return $globalCount;
    }

    /**
     * Returns HTML to display scan results, the value of $step matches a WOT rating range (very poor => 0 , poor => 1, etc.)
     * @return mixed
     */
    public function displayResults()
    {

        global $wpdb;
        $current_scan = $this->get_current_scan();

        //No previous scan
        if( ! isset($current_scan['id']) )
        {
            $response['noscan'] = true;
            $response['html'] = '<center><div class="mw_alert mw_alert-info text-center width60">'. sprintf( __('No previous scan to display!', $this->plugin_name)).'</div></center>';
            return $response;
        }

        $scan_id = $current_scan['id'];
        @define('ROOTPATH', __DIR__);
        require_once(ROOTPATH.'/../Class/DrawHTML.php');
        $DrawHTML = new DrawHTML($this->plugin_name);
        for($step = 0 ; $step <= 5 ; $step++ )
        {
            switch($step)
            {
                case 5:
                    $query = "trust >= 80 ";
                    $key = "excellent";
                    $title = "Excellent rating";
                    break;
                case 4:
                    $query = "trust < 80 AND trust >= 60 ";
                    $title = "Good rating";
                    $key = "good";
                    break;
                case 2:
                    $query = "trust < 60 AND trust >= 40 ";
                    $title = "Unsatisfactory rating";
                    $key = "unsatisfactory";
                    break;
                case 1:
                    $query = "trust < 40 AND trust >= 20 ";
                    $title = "Poor rating";
                    $key = "poor";
                    break;
                case 0:
                    $query = "trust < 20 AND trust >= 0 ";
                    $title = "Very poor rating";
                    $key = "very_poor";
                    break;
                case 3:
                    $query = "trust < 0";
                    $title = "Unknown rating";
                    $key = "unknown";
                    break;
                default:
                    $query = "trust < 0";
                    $title = "Links with an unknown rating";
                    $key = "unknown";
            }
            $scan_results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . 'matiks_wot_scan_results'." WHERE ".$wpdb->prefix ."matiks_wot_scans_id = '".$scan_id."' AND ".$query. " ORDER BY post_id, comment_id DESC", OBJECT );
            $response[$key] = $DrawHTML->postbox($title,$step,$scan_results,($step==0?$this->globalCount():null));

        }
        $response['author'] = "";
        if( isset($current_scan['user_id'])  )
        {
            $response['author'] = get_the_author_meta( 'display_name', $current_scan['user_id'] );
        }
        $response['lastAnalyze']  = sprintf( __('Last analyze &nbsp;&nbsp; %1$s &nbsp;&nbsp; by &nbsp;&nbsp;<b>%2$s</b>'), $current_scan['date'], $response['author']);

        $response['date'] = $current_scan['date'];
        return $response;
    }
}