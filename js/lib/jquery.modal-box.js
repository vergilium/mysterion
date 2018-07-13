/**
 * JQuery Plugin for a modal box
 * Will create a simple modal box with all HTML and styling
 *
 * Created by seriy-coder based on JQuery Plugin for 
 * a modal box by Paul Underwood [http://www.paulund.co.uk]
 * 02.08.13 / 10:30 (v1.0)
 * E:mail seriy-coder@ya.ru
 * Avaiable for free download from http://pastebin.com/YqsirVm8
 */

(function($){

	// Defining our jQuery plugin

	$.fn._modal_box = function(prop){

		// Default parameters

		var options = $.extend({
			height : "250",
			width : "500",
			title:"JQuery Modal Box",
			description: "Example of a modal box.",
			top: "20%",
			left: "30%",
			has_close: "false",
			bg_color: "rgba(0,0,0,0.6)",
			show_style: "fadeIn()",
			show_me: function(){
					add_block_page();
					add_popup_box();
					add_styles();
					$('._modal_box').fadeIn();
					//console.log('zzz');
				},
			hide_me: function(){
				$(this).parent().fadeOut().remove();
				$('._block_page').fadeOut().remove();
			},
			modify_me: function(ntitle,nmessage){
				 if (options.title==ntitle && options.description==nmessage) return;
				 $('._inner_modal_box').html("<h2>"+ntitle+"</h2><p>"+nmessage+"</p>");
			},
		},prop);
		
		/**
		 * Add styles to the html markup
		 */
		 function add_styles(){			
			$('._modal_box').css({ 
				'position':'absolute', 
				'left':options.left,
				'top':options.top,
				'display':'none',
				'height': options.height + 'px',
				'width': options.width + 'px',
				'border':'1px solid #fff',
				'box-shadow': '0px 2px 7px #292929',
				'-moz-box-shadow': '0px 2px 7px #292929',
				'-webkit-box-shadow': '0px 2px 7px #292929',
				'border-radius':'15px',
				'-moz-border-radius':'15px',
				'-webkit-border-radius':'15px',
				'background': '#f00', 
				'z-index':'50',
			});
			if (options.has_close=="true"){
				$('._modal_close').css({
					'position':'relative',
					'top':'-25px',
					'left':'20px',
					'float':'right',
					'display':'block',
					'height':'50px',
					'width':'50px',
					'background': 'url(images/close.png) no-repeat',
				});
			}
			$('._block_page').css({
				'position':'absolute',
				'top':'0',
				'left':'0',
				'background-color':options.bg_color,
				'height':'100%',
				'width':'100%',
				'z-index':'10'
			});
			$('._inner_modal_box').css({
				'background-color':'#fff',
				'height':(options.height - 50) + 'px',
				'width':(options.width - 50) + 'px',
				'padding':'17px',
				'margin':'8px',
				'border-radius':'10px',
				'-moz-border-radius':'10px',
				'-webkit-border-radius':'10px'
			});
			$('._inner_modal_box h2').css({
				'color':'#f00'
			});
		}
		
		 /**
		  * Create the block page div
		  */
		 function add_block_page(){
			var block_page = $('<div class="_block_page"></div>');	
			$(block_page).appendTo('body');
		}
		 	
		 /**
		  * Creates the modal box
		  */
		 function add_popup_box(){
			 var pop_up = $('<div class="_modal_box"><a href="#" class="_modal_close"></a><div class="_inner_modal_box"><h2>' + options.title + '</h2><p>' + options.description + '</p></div></div>');
			 $(pop_up).appendTo('._block_page');
		}
		return options;
	};
	
})(jQuery);
