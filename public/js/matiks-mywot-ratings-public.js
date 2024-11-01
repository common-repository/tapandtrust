(function( $ ) {
	'use strict';

	jQuery.post(
		params.ajaxurl,
		{
			'action': 'matiks_net_wot_ratings',
			'post_id': params.post_id,
			'wot_ratings_nonce': params.wot_ratings_nonce
		},
		function(resp){
			var response;
			try {
				response = eval('(' + resp + ')');
			} catch (e) {
				response = resp;
			}
			var elem, trust, child;
			for(var domain in response.domains )
			{
				if( response.block == 0 )
					elem = $("a[mw_ratings='" + domain + "']");
				else
					elem = $("span[mw_ratings='" + domain + "']");
				trust = response.domains[domain]['trust'];
				child = response.domains[domain]['child'];
				var wot_icon = "";
				if( trust >= 80 )
				{
					wot_icon = "matiks_wot_excellent";
				}
				else if(trust >= 60 && trust < 80 )
				{
					wot_icon = "matiks_wot_good";
				}
				else if(trust >= 40 && trust < 60 )
				{
					wot_icon = "matiks_wot_unsatisfactory";
				}
				else if(trust >= 20 && trust < 40 )
				{
					wot_icon = "matiks_wot_poor";
				}
				else if(trust >= 0 && trust < 20 )
				{
					wot_icon = "matiks_wot_very_poor";
				}
				else
				{
					wot_icon = "matiks_wot_unknown";
				}
				elem.append($("<div title='"+domain+" scorecard' class='wot-icon "+wot_icon+"' onclick='window.open(\"https://www.mywot.com/en/scorecard/"+domain+"\")'></div>"))
				var $li;
				if( response.block )
				{
					if( (trust >= response.min_trust && child >= response.min_child) || (trust == -1 && response.unknown == 0) )
					{
						$('span[mw_ratings="'+domain+'"]').replaceWith(function(){
							$li = $("<a>", {html: $(this).html()});
							$.each(this.attributes, function(i, attribute){
								$li.attr(attribute.name, attribute.value);
							});
							return $li;
						})
					}
				}
				else
				{

					if(  (0 <= trust && trust < response.min_trust) || (0 <= child && child < response.min_child) || (trust == -1 && response.unknown == 1) )
					{
						$('a[mw_ratings="'+domain+'"]').replaceWith(function(){
							$li = $("<span>", {html: $(this).html()});
							$.each(this.attributes, function(i, attribute){
								$li.attr(attribute.name, attribute.value);
							});
							return $li;
						})
					}
				}
			}
			$("#comments .comment-author a").each(function() {
				var element = $(this);
				var href = this.href;
				if($.trim(href) != "")
				{
					var mw_ratings = matiks_net_getUrlParameter(href,'mw_ratings');
					var mw_redirect = matiks_net_getUrlParameter(href,'mw_redirect');
					if(typeof response.domains[mw_ratings] != "undefined"){
						trust = response.domains[mw_ratings]['trust'];
						child = response.domains[mw_ratings]['child'];

						if( mw_ratings != null && mw_redirect != null )
						{
							element.attr('href',mw_redirect);
							element.attr("mw_ratings",mw_ratings);
							var wot_icon = "";
							if( trust >= 80 )
							{
								wot_icon = "matiks_wot_excellent";
							}
							else if(trust >= 60 && trust < 80 )
							{
								wot_icon = "matiks_wot_good";
							}
							else if(trust >= 40 && trust < 60 )
							{
								wot_icon = "matiks_wot_unsatisfactory";
							}
							else if(trust >= 20 && trust < 40 )
							{
								wot_icon = "matiks_wot_poor";
							}
							else if(trust >= 0 && trust < 20 )
							{
								wot_icon = "matiks_wot_very_poor";
							}
							else
							{
								wot_icon = "matiks_wot_unknown";
							}
							element.append($("<div title='"+mw_ratings+" scorecard' class='wot-icon "+wot_icon+"' onclick='window.open(\"https://www.mywot.com/en/scorecard/"+mw_ratings+"\")'></div>"))


							if(  (0 <= trust && trust < response.min_trust) || (0 <= child && child < response.min_child) || (trust == -1 && response.unknown == 1) )
							{
								element.replaceWith(function(){
									$li = $("<span>", {html: $(this).html()});
									$.each(this.attributes, function(i, attribute){
										$li.attr(attribute.name, attribute.value);
									});
									return $li;
								})
							}

						}
					}
					else
					{
						element.replaceWith(function(){
							$li = $("<span>", {html: $(this).html()});
							$.each(this.attributes, function(i, attribute){
								$li.attr(attribute.name, attribute.value);
							});
							return $li;
						})
					}

				}

			});

		}
	);

	var matiks_net_getUrlParameter = function matiks_net_getUrlParameter(url, sParam) {
		var results = new RegExp('[\?&]' + sParam + '=([^&#]*)').exec(url);
		if (results==null){
			return null;
		}
		else{
			return results[1] || 0;
		}
	};

})( jQuery );
