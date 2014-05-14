/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
test('options', function() {
	expect(3);

	var element = $('<div>');
	element.appendTo('body');
	element.loader();
	element.loader('show');
	equal( element.find('p').text(), 'Please wait...', '.loader() text matches' );
	equal( element.find('img').prop('src').split('/').pop(), 'icon.gif', '.loader() icons match' );
	equal( element.find('img').prop('alt'), 'Loading...', '.loader() image alt text matches' );
	element.loader('destroy');

});

test( 'element init', function() {
	expect(1);

	var element = $('<div>');
	element.appendTo('body');
	element.loader();
	element.loader('show');
    equal(element.is(':mage-loader'), true, '.loader() init on element');
    element.loader('destroy');

});

test( 'body init', function() {
	expect(1);

	//Initialize Loader on Body
	var body = $('body').loader();
    body.loader('show');
    equal(body.is(':mage-loader'), true, '.loader() init on body');
    body.loader('destroy');
});

test( 'show/hide', function() {
	expect(3);

	var element = $('<div>');
	element.appendTo('body');
	element.loader();

	//Loader show
	element.loader('show');
	equal($('.loading-mask').is(':visible'), true, '.loader() open');

	//Loader hide
	element.loader('hide');
	equal($('.loading-mask').is( ":hidden" ), true, '.loader() closed' );

	//Loader hide on process complete
    element.loader('show');
    element.trigger('processStop');
    equal($('.loading-mask').is('visible'), false, '.loader() closed after process');

    element.loader('destroy');

});

test( 'destroy', function() {
	expect(1);

	var element = $("#loader").loader();
	element.loader('show');
    element.loader('destroy');
    equal( $('.loading-mask').is(':visible'), false, '.loader() destroyed');

});