jQuery(document).ready(function(){
	jQuery( 'table.wc_order_category_sort tbody' ).sortable({
		update: function( event, ui ) {console.log(ajaxurl);}
	});
});