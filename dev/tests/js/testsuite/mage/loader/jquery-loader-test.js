/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
test('options', function() {
	expect(3);

	var element = $("#loader").loader({
		icon: 'icon.gif',
		texts: {
			imgAlt: 'Image Text',
			loaderText: 'Loader Text'
		},
		template: '<div class="loading-mask" data-role="loader"><div class="loader"><img alt="{{imgAlt}}" src="{{icon}}"><p>{{loaderText}}</p></div></div>'
	});
	element.loader('show');
	equal( element.find('p').text(), 'Loader Text', '.loader() text matches' );
	equal( element.find('img').prop('src').split('/').pop(), 'icon.gif', '.loader() icons match' );
	equal( element.find('img').prop('alt'), 'Image Text', '.loader() image alt text matches' );
	element.loader('destroy');

});

test( 'element init', function() {
	expect(1);

	//Initialize Loader on element
	var element = $("#loader").loader({
		icon: 'icon.gif',
		texts: {
			imgAlt: 'Image Text',
			loaderText: 'Loader Text'
		},
		template: '<div class="loading-mask" data-role="loader"><div class="loader"><img alt="{{imgAlt}}" src="{{icon}}"><p>{{loaderText}}</p></div></div>'
	});
	element.loader('show');
    equal(element.is(':mage-loader'), true, '.loader() init on element');
    element.remove();

});

test( 'body init', function() {
	expect(1);

	//Initialize Loader on Body
	var body = $('body').loader();
    body.loader('show');
    equal(true, $('body div:first').is('.loading-mask'));
    $('body').find('.loading-mask:first').remove();

});

test( 'show/hide', function() {
	expect(3);

	var element = $('body').loader();

	//Loader show
	element.loader('show');
	equal($('.loading-mask').is(':visible'), true, '.loader() open');

	//Loader hide
	element.loader('hide');
	equal($('.loading-mask').is( ":hidden" ), false, '.loader() closed' );

	//Loader hide on process complete
    element.loader('show');
    element.trigger('processStop');
    equal($('.loading-mask').is('visible'), false, '.loader() closed after process');

    element.find('.loading-mask').remove();

});

test( 'destroy', function() {
	expect(1);

	var element = $("#loader").loader({
		icon: 'icon.gif',
		texts: {
			imgAlt: 'Image Text',
			loaderText: 'Loader Text'
		},
		template: '<div class="loading-mask" data-role="loader"><div class="loader"><img alt="{{imgAlt}}" src="{{icon}}"><p>{{loaderText}}</p></div></div>'
	});
	element.loader('show');
    element.loader('destroy');
    equal( $('.loading-mask').is(':visible'), false, '.loader() destroyed');

});