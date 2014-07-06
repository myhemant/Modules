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
		$('.horizontal-tabs').append(PreButton );
		$('.horizontal-tabs').append(NextButton );
		$("#edit-prev").hide();
        $('#edit-next', context).click(function (e) {
			e.preventDefault();
			var t = $('.horizontal-tabs-panes fieldset.horizontal-tabs-pane').length;
			var ci = $(".horizontal-tabs-panes fieldset.horizontal-tabs-pane").not('.horizontal-tab-hidden').index();
			$('.horizontal-tabs-panes fieldset.horizontal-tabs-pane').eq(ci).addClass('horizontal-tab-hidden');
			$('.horizontal-tabs-panes fieldset.horizontal-tabs-pane').eq(ci+1).removeClass('horizontal-tab-hidden');	
			$(".horizontal-tabs-list li.selected").removeClass('selected').next('li').addClass('selected');
			$('html,body').animate({scrollTop: (0)}, 500);
			var ct = $(".horizontal-tabs-panes fieldset.horizontal-tabs-pane").not('.horizontal-tab-hidden').index();				
			var last =  (t-1 == ct ) ? $("#edit-next").hide() : $("#edit-next").show();
			var first = (ct == 0)   ?  $("#edit-prev").hide() : $("#edit-prev").show();
			return false
		});
		$('#edit-prev', context).click(function (e) {
			e.preventDefault();
			var t = $('.horizontal-tabs-panes fieldset.horizontal-tabs-pane').length;
			var ci = $(".horizontal-tabs-panes fieldset.horizontal-tabs-pane").not('.horizontal-tab-hidden').index();
			// Hide the current tab and show the previous tab
			$('.horizontal-tabs-panes fieldset.horizontal-tabs-pane').eq(ci).addClass('horizontal-tab-hidden');
			$('.horizontal-tabs-panes fieldset.horizontal-tabs-pane').eq(ci-1).removeClass('horizontal-tab-hidden');
	
			$(".horizontal-tabs-list li.selected").removeClass('selected').prev('li').addClass('selected');
			$('html,body').animate({scrollTop: (0)}, 500);	
			// Control the Next Pre Buttons
			var ct = $(".horizontal-tabs-panes fieldset.horizontal-tabs-pane").not('.horizontal-tab-hidden').index();
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
		
		var i = jQuery(".horizontal-tabs-panes fieldset.horizontal-tabs-pane").not('.horizontal-tab-hidden').index();
		this.showButton(i);
	},
	showButton: function(i){ 		
		var ind = (i == 0) ? $("#edit-prev").hide() : $("#edit-prev").show();  
	}	
  }  
})(jQuery);