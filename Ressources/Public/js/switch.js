!function ($){
	"use strict";
	
	//CHECKBOXSWITCH CLASS DEFINITION
	//=======================
	
	var Checkboxswitch = function (element) {
		this.$element = $(element);
	}
	
	//CHECKBOXSWITCH PLUGIN DEFINITION
	//========================
	
	$.fn.checkboxswitch = function (option) {
		return this.each(function(){
				var $this = $(this),
				data = $this.data('jbl.checkboxswitch');
			if (!data) $this.data('jbl.checkboxswitch', (data = new Checkboxswitch(this)));
			if (typeof option == 'boolean') data.swap(option)
		})
	};
	
	Checkboxswitch.prototype.swap = function(value){
		var $checks = this.$element.find('.to-swap');
		$checks.prop('checked', value);	
	}
	
	//CHECKBOXSWITcH DATA-API
	// ====================

	$(document).on('change', '[data-toggle="checkboxswitch"]', function (e) {
	    alert('wsap!');
		var $this = $(this);
	    var $target = $($this.data('target'));
	    var value = $this.prop('checked');
	    e.preventDefault();
	    $target.checkboxswitch(value);
	});
	$(document).on('change','[data-toggle="checkreset"]',function(e){
		var $this = $(this);
		var $target = $($this.data('target'));
		var $target2 = $($target.data('target'));
		e.preventDefault();
		if($target2.find('.to-swap:checked').length == $target2.find('.to-swap').length){
			$target.prop('checked',true);
		}else{
			$target.prop('checked',false);
		}
	});
	
}(window.jQuery)