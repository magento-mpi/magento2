define([
  'ko',
  'jquery',
  'Magento_Ui/js/framework/ko/template/engine',
  'Magento_Ui/js/framework/ko/bind/date',
  'Magento_Ui/js/framework/ko/bind/autocomplete',
  'Magento_Ui/js/framework/ko/bind/on',
  'Magento_Ui/js/framework/ko/bind/scope'
], function (ko, $, templateEngine) {
  ko.setTemplateEngine(templateEngine);

  return {
    apply: function () {
      ko.applyBindings();
    }
  }
});