(function ($) {
/**
 * This script use to perform next pre event with field group tabs
 * tab.
 *
 * Each tab have some field which is traverse by next pre buttons
 */
  Drupal.behaviors.field_group_nextpre = {	
    attach: function (context, settings) { 
		var PreButton = Drupal.settings.nextpre_settings.pre_button;
		var NextButton = Drupal.settings.nextpre_settings.next_button;				
		$('.field-group-tabsnp-wrapper .vertical-tabs-panes').append(PreButton );
		$('.field-group-tabsnp-wrapper .vertical-tabs-panes').append(NextButton );
		$("#edit-prev").hide();		
        $('#edit-next', context).click(function (e) {
			e.preventDefault();	
			var t = $('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset.vertical-tabs-pane').length;
			var ct = $('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset:visible').index();	
			$('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset:visible').hide().next('fieldset').show();
			$(".vertical-tabs-list li.selected").removeClass('selected').next('li').addClass('selected');
			$('html,body').animate({scrollTop: (0)}, 500);
			var last =  (t-1 == ct ) ? $("#edit-next").hide() : $("#edit-next").show();
			var first = (ct == 0)   ?  $("#edit-prev").hide() : $("#edit-prev").show();
			return false
		});
		$('#edit-prev', context).click(function (e) {
			e.preventDefault();
			var t = $('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset.vertical-tabs-pane').length;
			var ct = $('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset:visible').index();	
			// Hide the current tab and show the previous tab
			$('.field-group-tabsnp-wrapper .vertical-tabs-panes fieldset:visible').hide().prev('fieldset').show();
			$(".vertical-tabs-list li.selected").removeClass('selected').prev('li').addClass('selected');			
			$('html,body').animate({scrollTop: (0)}, 500);	
			// Control the Next Pre Buttons
			console.log(t);console.log(ct);
			var last =  (t-1 == ct ) ? $("#edit-next").hide() : $("#edit-next").show();
			var first = (ct == 0)   ?  $("#edit-prev").hide() : $("#edit-prev").show();
			return false;
		});
		$('.horizontal-tabs-list li a', context).click(function (e) {
			 $(".horizontal-tabs-pane").removeAttr("style"); 
			 var t = jQuery('.horizontal-tabs-panes fieldset.horizontal-tabs-pane').length;
			 var ct = jQuery(".horizontal-tabs-panes fieldset.horizontal-tabs-pane").not('.horizontal-tab-hidden').index();
			 var last =  (t-1 == ct ) ? $("#edit-next").hide() : $("#edit-next").show();
			 var first = (ct == 0)   ?  $("#edit-prev").hide() : $("#edit-prev").show();	
		});
		
		var i = jQuery(".field-group-tabsnp-wrapper .vertical-tabs-panes").not('.horizontal-tab-hidden').index();
		//this.showButton(i);
	},
	showButton: function(i){ 		
		var ind = (i == 0) ? $("#edit-prev").hide() : $("#edit-prev").show();  
	}	
  }  
})(jQuery);