
var doc = $(document);


function list_submit(num) {
	$('#show-list').find('#start_from').val(num);
	$('#show-list').submit();
	return false;
}

doc
	.on('click', '.expand-channel', function() {
		var $this = $(this),
			text = $this.text(),
			expandText = $this.data('expand'),
			collapseText = $this.data('collapse');

		$(this).parent().next('ul').slideToggle(500);
		if (expandText == text) {
			$(this).text(collapseText);
		} else {
			$(this).text(expandText);

		}
	})
	.on('change', '#mass_action', function() {
		if ($(this).val() == 'test') {
			var checkedEls = $('[name="items[]"]'),
				_ElArr = [],
				_out;

			$.each(checkedEls, function(index, val) {
				if ($(this).is(':checked')) {
					_ElArr.push($(val).val());
				};
			});
			_out = _ElArr.join(',');
			window.open('/autorss.php?test=1&fulldebug=1&id='+_out+'&pass=123');
		};
		if ($(this).val() == 'delete') {
			$('#show-list').prepend('<input type="hidden" name="action" value="delete" />').submit();
		};

	})
	.on('change', '[name="items[]"]', function() {
		$('#mass_action').prop('selectedIndex',0).trigger('refresh');
	});

jQuery(document).ready(function ($) {

	$('.styler').styler();
	$('.debug-item').find('textarea').autosize();

	$('.debug-item').on('click', '.show-item', function() {
		$(this).parent().next().find('.hide').slideToggle(500);
	});


	$('.main-checkbox').on('change', function () {
		var itemData = $(this).data('checkboxes'),
			item = $(itemData + ':enabled');
		if ($(this).prop('checked')) {
			item.prop('checked', true).trigger('change');
		}
		else {
			item.prop('checked', false).trigger('change');
		}
	});
	
	
	/*! http://dimox.name/beautiful-tooltips-with-jquery/ */
	$('.ttp').each(function(){var el=$(this);var title=el.attr('title');if(title&&title!=''){el.attr('title','').append('<div>'+title+'</div>');var width=el.find('div').width();var height=el.find('div').height();el.hover(function(){el.find('div').clearQueue().delay(200).animate({width:width+20,height:height+20},200).show(200).animate({width:width,height:height},200);},function(){el.find('div').animate({width:width+20,height:height+20},150).animate({width:'hide',height:'hide'},150);}).mouseleave(function(){if(el.children().is(':hidden'))el.find('div').clearQueue();});}}); 
});