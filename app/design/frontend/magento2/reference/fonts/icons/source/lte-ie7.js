/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* Use this script if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'icomoon\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-search' : '&#xe005;',
			'icon-pencil' : '&#xe006;',
			'icon-menu' : '&#xe007;',
			'icon-location' : '&#xe008;',
			'icon-info_icon' : '&#xe00a;',
			'icon-flag' : '&#xe00c;',
			'icon-expand' : '&#xe00d;',
			'icon-exclamation_mark' : '&#xe00e;',
			'icon-dropdown_arrow' : '&#xe010;',
			'icon-collapse' : '&#xe011;',
			'icon-close' : '&#xe012;',
			'icon-checkmark' : '&#xe013;',
			'icon-cart' : '&#xe014;',
			'icon-arrow2_up' : '&#xe015;',
			'icon-arrow2_r' : '&#xe016;',
			'icon-arrow2_l' : '&#xe017;',
			'icon-arrow2_down' : '&#xe018;',
			'icon-arrow1_up' : '&#xe019;',
			'icon-arrow1_r' : '&#xe01a;',
			'icon-arrow1_l' : '&#xe01b;',
			'icon-arrow1_down' : '&#xe01c;',
			'icon-wishlist' : '&#xe01d;',
			'icon-comment' : '&#xe01e;',
			'icon-comment-reflected' : '&#xe01f;',
			'icon-list' : '&#xe002;',
			'icon-Layer-11' : '&#xe003;',
			'icon-Layer-9' : '&#xe004;',
			'icon-trash' : '&#xe000;',
			'icon-newsletter' : '&#xe001;'
		},
		els = document.getElementsByTagName('*'),
		i, attr, html, c, el;
	for (i = 0; i < els.length; i += 1) {
		el = els[i];
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
};