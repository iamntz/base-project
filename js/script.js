jQuery.fn.rdy = function(func){
	this.length && func.apply(this);
	return this;
};
jQuery(document).ready(function($){
	$('html').removeClass('no-js');
	/*== placeholder for browsers that sucks ==*/
	var fakeInput = document.createElement("input"),
			placeHolderSupport = ("placeholder" in fakeInput),
			clearValue = function (e) {
				if ($(e).val() === $(e).data('placeholder')) {
					$(e).val('');
				}
			};
	if (!placeHolderSupport) {
		$('input[placeholder]').each(function(){
			var searchField = $(this),
					originalText = searchField.attr('placeholder'),
					val = this.value;
			searchField.data('placeholder', originalText);
			if(val == '') { this.value = originalText; }else {
				searchField.addClass("placeholder")
			}
			
			searchField.bind("focus", function () { this.value = ''; }).bind("blur", function () {
				if (this.value.length === 0) {
					$(this).val(originalText).addClass("placeholder");
				}
			});
		});

		// Empties the placeholder text at form submit if it hasn't changed
		$("form").bind("submit", function () {
			clearValue($('input[placeholder]', this));
		});

		// Clear at window reload to avoid it stored in autocomplete
		$(window).bind("unload", function () {
			clearValue($('input[placeholder]', this));
		});
	}
	/*== end placeholder ==*/
});


