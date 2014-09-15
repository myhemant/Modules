(function ($) {
/**
 * This script use to perform next pre event with field group vertical tabs
 * tab.
 *
 * Each tab have some field which is traverse by next pre buttons
 */
  Drupal.behaviors.field_group_nextpre = {	
    attach: function (context, settings) { 
		var PreButtonv = Drupal.settings.nextpre_settings.pre_button;
		var NextButtonv = Drupal.settings.nextpre_settings.next_button;				
		$('.field-group-tabsnp-wrapper .vertical-tabs-panes').append(PreButtonv );
		$('.field-group-tabsnp-wrapper .vertical-tabs-panes').append(NextButtonv );
		$(".vertical-tabs-panes #edit-prev").hide();		
        $('.vertical-tabs-panes #edit-next', context).click(function (e) {
			e.preventDefault();	
			var t = $('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset.vertical-tabs-pane').length;
			var ct = $('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset:visible').index();	
			$('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset:visible').hide().next('fieldset').show();
			$(".vertical-tabs-list li.selected").removeClass('selected').next('li').addClass('selected');
			//$('html,body').animate({scrollTop: (0)}, 500);
			var last =  (t-1 == ct ) ? $(".vertical-tabs-panes #edit-next").hide() : $(".vertical-tabs-panes #edit-next").show();
			var first = (ct == 0)   ?  $(".vertical-tabs-panes #edit-prev").hide() : $(".vertical-tabs-panes #edit-prev").show();
			return false
		});
		$('.vertical-tabs-panes #edit-prev', context).click(function (e) {
			e.preventDefault();
			var t = $('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset.vertical-tabs-pane').length;
			ct = '';
			$('.field-group-tabsnp-wrapper ul.vertical-tabs-list li').each(function(i,v) { if(jQuery(this).hasClass('selected')) {ct =i;}});
			// Hide the current tab and show the previous tab
			$('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset:visible').hide().prev('fieldset').show();
			$(".vertical-tabs-list li.selected").removeClass('selected').prev('li').addClass('selected');			
			//$('html,body').animate({scrollTop: (0)}, 500);	
			// Control the Next Pre Buttons
			var last =  (t == ct ) ? $(".vertical-tabs-panes #edit-next").hide() : $(".vertical-tabs-panes #edit-next").show();
			var first = (ct == 1)   ?  $(".vertical-tabs-panes #edit-prev").hide() : $(".vertical-tabs-panes #edit-prev").show();
			return false;
		});
		$('.vertical-tabs-list li a', context).click(function (e) {
			 $(".vertical-tabs-panes").removeAttr("style"); 
			 var t = jQuery('.field-group-tabsnp-wrapper .vertical-tabs-list li').length;
			 ct = '';
			 $('.field-group-tabsnp-wrapper ul.vertical-tabs-list li').each(function(i,v) { if(jQuery(this).hasClass('selected')) {ct =i;}});
			 var last =  (t-1 == ct ) ? $(".vertical-tabs-panes #edit-next").hide() : $(".vertical-tabs-panes #edit-next").show();
			 var first = (ct == 0)   ?  $(".vertical-tabs-panes #edit-prev").hide() : $(".vertical-tabs-panes #edit-prev").show();	
		});
		
		var i = jQuery(".field-group-tabsnp-wrapper .vertical-tabs-panes").not('.vertical-tab-hidden').index();
	
    
    },
	
  }  
})(jQuery);