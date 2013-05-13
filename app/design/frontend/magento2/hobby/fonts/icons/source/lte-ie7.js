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
		el.innerHTML = '<span style="font-family: \'recon\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-checkmark' : '&#xe000;',
			'icon-close' : '&#xe001;',
			'icon-collapse' : '&#xe002;',
			'icon-dropdown_arrow_down' : '&#xe003;',
			'icon-envelope' : '&#xe004;',
			'icon-error' : '&#xe005;',
			'icon-grid' : '&#xe006;',
			'icon-list' : '&#xe007;',
			'icon-next' : '&#xe008;',
			'icon-ok' : '&#xe009;',
			'icon-oops' : '&#xe00a;',
			'icon-open' : '&#xe00b;',
			'icon-previous' : '&#xe00c;',
			'icon-quotation_mark' : '&#xe00d;',
			'icon-search' : '&#xe00e;',
			'icon-sort-arrow-down' : '&#xe00f;',
			'icon-sort-arrow-up' : '&#xe010;',
			'icon-star' : '&#xe011;',
			'icon-trash' : '&#xe012;'
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
