function initRuleParams(event) {
	var params = $$('.rule-param'), i, label, elem, cont;
	for (var i=0; i<params.length; i++) {
		cont = params[i];
		label = Element.down(params[i], '.label');
		elem = Element.down(params[i], '.element').down();
		Event.observe(label, 'click', showRuleParamInputField.bind(cont));
		Event.observe(elem, 'change', hideRuleParamInputField.bind(cont));
		Event.observe(elem, 'blur', hideRuleParamInputField.bind(cont));
	}
}

function showRuleParamInputField(event) {
	Element.addClassName(this, 'rule-param-edit');
	Element.down(this, '.element').down().focus();
}

function hideRuleParamInputField(event) {
	Element.removeClassName(this, 'rule-param-edit');
	var label = Element.down(this, '.label'), elem;

	elem = Element.down(this, 'select');
	if (elem) {
		label.innerHTML = elem.options[elem.selectedIndex].text;
	}

	elem = Element.down(this, 'input.input-text');
	if (elem) {
		label.innerHTML = elem.value;
	}
}

Event.observe(window, 'load', initRuleParams);