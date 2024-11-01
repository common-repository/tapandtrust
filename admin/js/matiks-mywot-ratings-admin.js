(function( $ ) {
	'use strict';




	var amountByQuery = 100;
	//AJAX call => get options
	$(document).on('click','#matiks_wot_analysis',function(){
		$('#wot_last_scan').addClass('hidden');
		$('#analyze_container').removeClass('hidden');
		$('#progressbar').val(0);
		$('.progress-value').html(Math.round(0) + '%');
		$('#analysis_no_selection').addClass('hidden');
		if( $(this).hasClass('disabled') )
			return false;
		if( !$('#matiks_wot_analysis_posts').is(':checked') && !$('#matiks_wot_analysis_comments').is(':checked') && !$('#matiks_wot_analysis_pages').is(':checked') )
		{
			$('#analyze_container').addClass('hidden');
			$('#analysis_no_selection').removeClass('hidden');
			return false;
		}
		$('#analysis_p').removeClass('hidden');

		$('#analyze_container').removeClass('hidden');
		$('#analysis_p_done').addClass('hidden');
		$('#analysis_p_domains').addClass('hidden');
		$('#analysis_p_domains_done').addClass('hidden');
		$('#result').addClass('hidden');
		$('#matiks_wot_analysis').addClass('disabled');
		$('#error').addClass('hidden');


		jQuery.post(
			params.ajaxurl,
			{
				'action': 'matiks_net_get_option',
				'inc_posts': $('#matiks_wot_analysis_posts').is(':checked'),
				'inc_pages': $('#matiks_wot_analysis_pages').is(':checked'),
				'inc_comments': $('#matiks_wot_analysis_comments').is(':checked'),
				'wot_analysis_nonce': params.wot_analysis_nonce
			},
			function(response){
				response = eval('(' + response + ')');
				if( response.error )
				{
					$('#error_message').html(response.error)
					$('#error').removeClass('hidden');
					$('#analysis_p').addClass('hidden');
					$('#matiks_wot_analysis').removeClass('disabled');
					$('#domains_analysis').addClass('hidden');

				}
				else
				{
					//Step: Get URLs
					$('#analysis_p').addClass('hidden');

					$('#analysis_p_done').html('<span class="dashicons dashicons-visibility"></span>&nbsp;' + response.message)
					$('#analysis_p_done').removeClass('hidden');
					matiks_net_get_urls(response);
				}

			}
		).error(function() {$('#error_message').html(response.error)
				$('#error').removeClass('hidden');
				$('#analysis_p').addClass('hidden');
				$('#matiks_wot_analysis').removeClass('disabled');
				$('#domains_analysis').addClass('hidden');
				return false; });
	})

	//AJAX call => get urls
	function matiks_net_get_urls(response)
	{
		$('#analysis_p_domains').removeClass('hidden');
		jQuery.post(
			params.ajaxurl,
			{
				'action': 'matiks_net_get_urls',
				'wot_analysis_nonce': params.wot_analysis_nonce
			},
			function(response){
				response = eval('(' + response + ')');

				if( response.error )
				{
					$('#error_message').html(response.error)
					$('#error').removeClass('hidden');
					$('#matiks_wot_analysis').removeClass('disabled');
					$('#domains_analysis').addClass('hidden');
				}
				else
				{
					$('#analysis_p_domains').addClass('hidden');
					$('#analysis_p_domains_done').html('<span class="dashicons dashicons-shield-alt"></span>&nbsp;' +  response.analyzedDomainsText);
					$('#analysis_p_domains_done').removeClass('hidden');
					if( response.analyzedDomains < 1 )
					{
						$('#matiks_wot_analysis').removeClass('disabled');
						$('#wot_last_scan').addClass('hidden');

						return;
					}
					var length = Math.ceil(response.analyzedDomains/amountByQuery)
					var step = 100/length;
					$('.progress-value').html('0 %');
					$('#domains_analysis').removeClass('hidden');
					matiks_net_get_domain_to_analyze(0,step);
				}
			}
		).error(function() {$('#error_message').html(response.error)
				$('#error').removeClass('hidden');
				$('#domains_analysis').addClass('hidden');
				$('#analysis_p_domains').addClass('hidden');
				$('#matiks_wot_analysis').removeClass('disabled');
				return false; });
	}

	//AJAX call => get domain to analyze
	function matiks_net_get_domain_to_analyze(i,step) {
		var progressbar = $('#progressbar')
		if (i*step > 100) {
			progressbar.val(100);
			$('.progress-value').html(Math.round(100) + '%');
		}

		jQuery.post(
			params.ajaxurl,
			{
				'action': 'matiks_net_analyze_domains',
				'step': step,
				'inc': i,
				'wot_analysis_nonce': params.wot_analysis_nonce
			},
			function(response){
				response = eval('(' + response + ')');
				if (response.error) {
					$('#error_message').html(response.error)
					$('#error').removeClass('hidden');
					$('#matiks_wot_analysis').removeClass('disabled');
					$('#domains_analysis').addClass('hidden');
					return false;
				}
				else
				{
					i++;
					$('#progressbar').val(i*step);
					$('.progress-value').html(Math.round(i*step) + '%');
					if( i*step < 100 )
					{
						matiks_net_get_domain_to_analyze(i,step)
					}
					else
					{
						matiks_net_display_results();
					}
				}
			}
		).error(function() {$('#error_message').html(response.error)
				$('#error').removeClass('hidden');
				$('#domains_analysis').addClass('hidden');
				$('#matiks_wot_analysis').removeClass('disabled');
				return false; });

	}

	//AJAX call => display result
	function matiks_net_display_results() {

		$('#wot_last_scan').removeClass('hidden');
		$('#scan_loader').removeClass('hidden');
		$('#result').addClass('hidden');

		jQuery.post(
			params.ajaxurl,
			{
				'action': 'matiks_net_display_results',
				'wot_analysis_nonce': params.wot_analysis_nonce
			},
			function(response){
				response = eval('(' + response + ')');
				if (response.error) {
					$('#scan_loader').addClass('hidden');
					$('#result').removeClass('hidden');
					$('#error_message').html(response.error)
					$('#error').removeClass('hidden');
					$('#domains_analysis').addClass('hidden');
					$('#matiks_wot_analysis').removeClass('disabled');
					return false;
				}
				else
				{
					if( response.noscan)
					{
						$('#no_result_aera').html(response.html);
						$('#scan_loader').addClass('hidden');
						$('#result').removeClass('hidden');
						$('#sections_aera').addClass('hidden');
					}
					else
					{
						if( typeof response.globalCount != "undefined" && response.globalCount < 1 )
						{
							$('#sections_aera').html(response.noresult);
							$('#scan_loader').addClass('hidden');
							$('#result').removeClass('hidden');
							return;
						}
						$('#last_analyze').html('<span class="dashicons dashicons-calendar-alt"></span> ' + response.lastAnalyze );
						$('#mw_vp_results').html(response.very_poor.html);
						$('#mw_p_results').html(response.poor.html);
						$('#mw_u_results').html(response.unknown.html);
						$('#mw_un_results').html(response.unsatisfactory.html);
						$('#mw_g_results').html(response.good.html);
						$('#mw_e_results').html(response.excellent.html);
						$('#sections_aera').removeClass('hidden');
						$('#no_result_aera').html("");
						$('#scan_loader').addClass('hidden');
						$('#result').removeClass('hidden');
						$('#matiks_wot_analysis').removeClass('disabled');
					}
					for( var k = 0 ; k <= 5 ; k++)
					{
						var headertext = [],
							headers = document.querySelectorAll("#table_responsive_"+k+" th"),
							tablebody = document.querySelector("#table_responsive_"+k+" tbody");

						for(var i = 0; i < headers.length; i++) {
							var current = headers[i];
							headertext.push(current.textContent.replace(/\r?\n|\r/,""));
						}
						if( tablebody != null)
						{
							for (var i = 0, row; row = tablebody.rows[i]; i++) {
								for (var j = 0, col; col = row.cells[j]; j++) {
									col.setAttribute("data-th", headertext[j]);
								}
							}
						}
					}

				}
			}
		).error(function() {$('#error_message').html(response.error)

				$('#error').removeClass('hidden');
				$('#matiks_wot_analysis').removeClass('disabled');
				$('#scan_loader').addClass('hidden');
				$('#domains_analysis').addClass('hidden');
				$('#result').removeClass('hidden');
				return false; });

	}


	$(document).on('click','.mw_btn_disapproved_comment',function(){

		var element = $(this);
		var comment_id = element.attr('comment_id');
		var parent = element.parent();
		parent.html('<span class="loader-green-sq"></span>');
		jQuery.post(
			params.ajaxurl,
			{
				'action': 'matiks_net_disapproved_comment',
				'comment_id': comment_id,
				'wot_analysis_nonce': params.wot_analysis_nonce
			},
			function(response){
				parent.css('text-align','center');
				parent.html('<span style="color: #3c763d !important;" class="dashicons dashicons-yes"></span> '+$('#matiks_net_data_trans').data('disapproved'));
				$('button[comment_id="'+comment_id+'"]').each(function() {
					var _p = $(this).parent();
					_p.css('text-align','center');
					_p.html('<span style="color: #3c763d !important;" class="dashicons dashicons-yes"></span> '+$('#matiks_net_data_trans').data('disapproved'));

				});
					}
		).error(function() {
				parent.css('text-align','center');
				parent.html('<span style="color: #fa1c1c !important;" class="dashicons dashicons-no">'+$('#matiks_net_data_trans').data('error')+'</span>');});
	})


	$(document).on('click','#wot_last_scan section h1',function(e){
		if( $(this).hasClass('mw_disabled'))
			return false;
		$(this).parents().siblings("section").addClass("ac_hidden");
		if( $(this).parents("section").hasClass("ac_hidden") )
			$(this).parents("section").removeClass("ac_hidden");
		else
			$(this).parents("section").addClass("ac_hidden");
		e.preventDefault();
	});

	$(document).ready(function()
	{

		if( $('#wot_last_scan').length > 0 )
		{
			matiks_net_display_results();
		}

		if( $("#slider_trust").length > 0 )
		{
			$('#slider_trust .rs-range-color').css('background-color',matiks_net_changeSliderColor($('#matiks-mywot-ratings-min_trust').val()))
			$('#slider_child .rs-range-color').css('background-color',matiks_net_changeSliderColor($('#matiks-mywot-ratings-min_child').val()))
			$("#slider_trust").roundSlider({
				sliderType: "min-range",
				editableTooltip: true,
				radius: 105,
				width: 16,
				value: $('#matiks-mywot-ratings-min_trust').val(),
				handleSize: 0,
				handleShape: "square",
				circleShape: "pie",
				startAngle: 315,
				tooltipFormat: function textTrust(args)
				{
					return "<strong>Min&nbsp;Trust</strong><br/>" + args.value;
				},
				change: function (e) {
					$('#matiks-mywot-ratings-min_trust').val(e.value);
					$('#slider_trust .rs-range-color').css('background-color',matiks_net_changeSliderColor(e.value));
				},
				drag:function(e){
					$('#slider_trust .rs-range-color').css('background-color',matiks_net_changeSliderColor(e.value));
				}
			});
			$("#slider_child").roundSlider({
				sliderType: "min-range",
				editableTooltip: true,
				radius: 105,
				width: 16,
				value: $('#matiks-mywot-ratings-min_child').val(),
				handleSize: 0,
				handleShape: "square",
				circleShape: "pie",
				startAngle: 315,
				tooltipFormat: function textTrust(args)
				{
					return "<strong>Min&nbsp;Child</strong><br/>" + args.value;
				},
				change: function (e) {
					$('#matiks-mywot-ratings-min_child').val(e.value);
					$('#slider_child .rs-range-color').css('background-color',matiks_net_changeSliderColor(e.value));
				},
				drag:function(e){
					$('#slider_child .rs-range-color').css('background-color',matiks_net_changeSliderColor(e.value));
				}
			});
			$('#slider_trust .rs-range-color').css('background-color',matiks_net_changeSliderColor($('#matiks-mywot-ratings-min_trust').val()))
			$('#slider_child .rs-range-color').css('background-color',matiks_net_changeSliderColor($('#matiks-mywot-ratings-min_child').val()))

		}
	});


	function matiks_net_changeSliderColor(value)
	{
		if( 0 <= value && value < 20 )
		{
			return "#fa1c1c";
		}
		else if( 20 <= value && value < 40 )
		{
			return "#ff5631";
		}
		else if( 40 <= value && value < 60 )
		{
			return "#ffd500";
		}
		else if( 60 <= value && value < 80 )
		{
			return "#8bd704";
		}
		else
		{
			return "#52b000";
		}
	}


})( jQuery );
