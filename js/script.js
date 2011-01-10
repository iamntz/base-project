jQuery.fn.rdy = function(func){
	this.length && func.apply(this);
	return this;
};
jQuery(document).ready(function($){
	$('html').removeClass('no-js');
	
});