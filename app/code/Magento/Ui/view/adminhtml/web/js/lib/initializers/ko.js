define([
	'ko',
	'jquery',
	'Magento_Ui/js/lib/ko/template/engine',
	'Magento_Ui/js/lib/ko/bind/date',
	'Magento_Ui/js/lib/ko/bind/autocomplete',
	'Magento_Ui/js/lib/ko/bind/on',
	'Magento_Ui/js/lib/ko/bind/scope',
    'Magento_Ui/js/lib/ko/bind/datepicker'
], function(ko, $, templateEngine) {

	ko.setTemplateEngine(templateEngine);
	ko.applyBindings();

});