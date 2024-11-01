=== Tap&Trust ===
Contributors: Matiks
Site: https://matiks.net
Tags: comments, spam, security, mywot, web of trust, block links
Requires at least: 4.3.1
Tested up to: 4.6
Stable tag: 4.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Tap&Trust detects malicious links in comments and blocks them (with the WOT API - Web Of Trust).


== Description ==


Tap&Trust allows to block links in comments having a poor reputation. A colored circle will be displayed at the right of each link.

You can defined the threshold of the trustworthiness and  the child safety to block links (a value between 0 and 100 corresponding to the minimum of the rating).

Also, a tool allows to analyze every links in your comments / posts /pages. You will be able to disapprove comments having links with a poor reputation.

Finally, if you want to claim your site on WOT (Web Of Trust), the plugin suggests a tool which will allow you to do it easly.

**You will need to have an account at WOT and to have an API key (all is free!)**

For more details:

MyWOT Thread: [https://www.mywot.com/en/forum/61574-mywot-wordpress-plugin][1]

Sites: [https://matiks.net/category/wordpress][2]

This plugin is not an official plugin of [Web Of Trust][3]


  [1]: https://www.mywot.com/en/forum/61574-mywot-wordpress-plugin
  [2]: https://matiks.net/category/wordpress
  [3]: https://www.mywot.com

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/tapandtrust` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= How it works =

Tap&Trust works with the MyWOT ([Web Of Trust][1]) API. Ratings come from a community of millions of members and also from trusted sources: [How WOT works][2].

To make this plugin works, you will need a WOT API key (free). If you don't have an account, the first step is to [create one][3]. Then, you only need to [claim an API key][4].


  [1]: https://www.mywot.com
  [2]: https://www.mywot.com/en/support/how-wot-works
  [3]: https://www.mywot.com/signup
  [4]: https://www.mywot.com/profile/api


= Lock all links before to know their rating with WOT =

If the WOT API does not work (bad API key or API is down, etc.) or if the visitor has disabled javascript and this option is activated, every links in comments will be disabled.
If this option is disabled (default) all links will be clickable if there is an error with the WOT API.

= Lock links having an unknown rating =

Some sites do not have a rating at WOT, so they have an unknown rating (grey doughnut icon).

It is possible to block this kind of links by checking the option.

By default, sites with an unknown ratings are clickable.

= Customize thresholds for disabling links =

There are two kind of ratings:

 - Trustworthiness
 - Child safety

That's why there are two counters where you can define your custom thresholds.

For that, just move  the central hand or enter a value for Trustworthiness and / or Child safety.

If one of the two ratings of a site is lower than these values, the link will be disabled.

Default value are 60/100 for both ratings.

== Screenshots ==

1. A link with a good reputation which is clickable (green circle) and an unsafe link with a red circle which is not clickable
2. Define the threshold to block links in comments by moving the central hand or by entering a value.
3. Quickly claim your site at WOT
4. Analyze your site to find malicious links in posts/pages/comments.
5. Disapprove comments having links with a poor reputation.


== Changelog ==

= 1.0.5 =
* Minor changes
 - Missing translation + picture
 - JS error
 - New name
 - Tested with WP 4.6

= 1.0.4 =
* First version uploaded

== Translations ==

* English
* Français

== Credits ==

Thank you to a WOT member who provided the domain http://wp-plugin.tapandtrust.co.uk to test the plugin.
[I.T. Mate](http://mysteryfcm.co.uk/)

Thank you to **Peter**, for his support and for his article [peterswebsafety](http://peterswebsafety.com/spams-scams-news-blog/)

Thank you to **Super Hero!** - For your help and support since I am a member at WOT.

Thank you to a member who designed the logo for Tap&Trust.

**Thank you to all members of the WOT community who participate to a safer Web.**