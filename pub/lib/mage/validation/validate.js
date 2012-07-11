/**
 * {license_notice}
 *
 * @category    validation
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint regexdash:true */
jQuery.validator.addMethod("allowContainerClassName", function (element) {
  if ( element.type == 'radio' || element.type == 'checkbox' ) {
    return $(element).hasClass('change-container-classname');
  }
}, '');

jQuery.validator.addMethod("validateNoHtmlTags", function (value) {
  return !/<(\/)?\w+/.test(value);
}, mage.localize.translate('HTML tags are not allowed'));

jQuery.validator.addMethod("validateSelect", function (value) {
  return ((value !== "none") && (value != null) && (value.length !== 0));
}, mage.localize.translate('Please select an option'));

jQuery.validator.addMethod("isEmpty", function (value) {
  return  (value === '' || (value == null) || (value.length === 0) || /^\s+$/.test(value));
}, mage.localize.translate('Empty Value'));

(function () {
  function isEmpty(value) {
    return  (value === '' || (value == null) || (value.length === 0) || /^\s+$/.test(value));
  }

  function isEmptyNoTrim(value) {
    return  (value === '' || (value == null) || (value.length === 0));
  }

  function parseNumber(value) {
    if ( typeof value != 'string' ) {
      return parseFloat(v);
    }
    var isDot = value.indexOf('.');
    var isComa = value.indexOf(',');
    if ( isDot != -1 && isComa != -1 ) {
      if ( isComa > isDot ) {
        value = value.replace('.', '').replace(',', '.');
      }
      else {
        value = value.replace(',', '');
      }
    }
    else if ( isComa != -1 ) {
      value = value.replace(',', '.');
    }
    return parseFloat(value);
  }

  jQuery.validator.addMethod("validateAlphanumWithSpaces", function (v) {
    return isEmptyNoTrim(v) || /^[a-zA-Z0-9 ]+$/.test(v);
  }, mage.localize.translate('Please use only letters (a-z or A-Z), numbers (0-9) or spaces only in this field'));

  jQuery.validator.addMethod("validateData", function (v) {
    return isEmptyNoTrim(v) || /^[A-Za-z]+[A-Za-z0-9_]+$/.test(v);
  }, mage.localize.translate('Please use only letters (a-z or A-Z), numbers (0-9) or underscore(_) in this field, first character should be a letter.'));

  jQuery.validator.addMethod("validateStreet", function (v) {
    return isEmptyNoTrim(v) || /^[ \w]{3,}([A-Za-z]\.)?([ \w]*\#\d+)?(\r\n| )[ \w]{3,}/.test(v);
  }, mage.localize.translate('Please use only letters (a-z or A-Z) or numbers (0-9) or spaces and # only in this field'));

  jQuery.validator.addMethod("validatePhoneStrict", function (v) {
    return isEmptyNoTrim(v) || /^(\()?\d{3}(\))?(-|\s)?\d{3}(-|\s)\d{4}$/.test(v);
  }, mage.localize.translate('Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.'));

  jQuery.validator.addMethod("validatePhoneLax", function (v) {
    return isEmptyNoTrim(v) || /^((\d[\-. ]?)?((\(\d{3}\))|\d{3}))?[\-. ]?\d{3}[\-. ]?\d{4}$/.test(v);
  }, mage.localize.translate('Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.'));

  jQuery.validator.addMethod("validateFax", function (v) {
    return isEmptyNoTrim(v) || /^(\()?\d{3}(\))?(-|\s)?\d{3}(-|\s)\d{4}$/.test(v);
  }, mage.localize.translate('Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.'));

  jQuery.validator.addMethod("validateEmail", function (v) {
    return isEmptyNoTrim(v) || /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(v);
  }, mage.localize.translate('Please enter a valid email address. For example johndoe@domain.com.'));

  jQuery.validator.addMethod("validateEmailSender", function (v) {
    return isEmptyNoTrim(v) || /^[\S ]+$/.test(v);
  }, mage.localize.translate('Please enter a valid email address. For example johndoe@domain.com.'));

  jQuery.validator.addMethod("validatePassword", function (v) {
    if ( v == null ) {
      return false;
    }
    var pass = $.trim(v);
    /*strip leading and trailing spaces*/
    if ( 0 === pass.length ) {
      return true;
    }
    /*strip leading and trailing spaces*/
    return !(pass.length > 0 && pass.length < 6);
  }, mage.localize.translate('Please enter 6 or more characters. Leading or trailing spaces will be ignored.'));

  jQuery.validator.addMethod("validateAdminPassword", function (v) {
    if ( v == null ) {
      return false;
    }
    var pass = $.trim(v);
    /*strip leading and trailing spaces*/
    if ( 0 === pass.length ) {
      return true;
    }
    if ( !(/[a-z]/i.test(v)) || !(/[0-9]/.test(v)) ) {
      return false;
    }
    if ( pass.length < 7 ) {
      return false;
    }
    return true;
  }, mage.localize.translate('Please enter 7 or more characters. Password should contain both numeric and alphabetic characters.'));

  jQuery.validator.addMethod("validateUrl", function (v) {
    if ( isEmptyNoTrim(v) ) {
      return true;
    }
    v = (v || '').replace(/^\s+/, '').replace(/\s+$/, '');
    return (/^(http|https|ftp):\/\/(([A-Z0-9]([A-Z0-9_-]*[A-Z0-9]|))(\.[A-Z0-9]([A-Z0-9_-]*[A-Z0-9]|))*)(:(\d+))?(\/[A-Z0-9~](([A-Z0-9_~-]|\.)*[A-Z0-9~]|))*\/?(.*)?$/i).test(v);

  }, mage.localize.translate('Please enter a valid URL. Protocol is required (http://, https:// or ftp://).'));

  jQuery.validator.addMethod("validateCleanUrl", function (v) {
    return isEmptyNoTrim(v) || /^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i.test(v) || /^(www)((\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i.test(v);

  }, mage.localize.translate('Please enter a valid URL. For example http://www.example.com or www.example.com'));

  jQuery.validator.addMethod("validateXmlIdentifier", function (v) {
    return isEmptyNoTrim(v) || /^[A-Z][A-Z0-9_\/-]*$/i.test(v);

  }, mage.localize.translate('Please enter a valid URL. For example http://www.example.com or www.example.com'));

  jQuery.validator.addMethod("validateSsn", function (v) {
    return isEmptyNoTrim(v) || /^\d{3}-?\d{2}-?\d{4}$/.test(v);

  }, mage.localize.translate('Please enter a valid social security number. For example 123-45-6789.'));

  jQuery.validator.addMethod("validateZip", function (v) {
    return isEmptyNoTrim(v) || /(^\d{5}$)|(^\d{5}-\d{4}$)/.test(v);

  }, mage.localize.translate('Please enter a valid zip code. For example 90602 or 90602-1234.'));

  jQuery.validator.addMethod("validateDateAu", function (v) {
    if (isEmptyNoTrim(v)) return true;
    var regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    if ( isEmpty(v) || !regex.test(v) ) return false;
    var d = new Date(v.replace(regex, '$2/$1/$3'));
    return ( parseInt(RegExp.$2, 10) == (1 + d.getMonth()) ) &&
      (parseInt(RegExp.$1, 10) == d.getDate()) &&
      (parseInt(RegExp.$3, 10) == d.getFullYear() );

  }, mage.localize.translate('Please use this date format: dd/mm/yyyy. For example 17/03/2006 for the 17th of March, 2006.'));

  jQuery.validator.addMethod("validateCurrencyDollar", function (v) {
    return isEmptyNoTrim(v) || /^\$?\-?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}\d*(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/.test(v);

  }, mage.localize.translate('Please enter a valid $ amount. For example $100.00.'));

  jQuery.validator.addMethod("validateNotNegativeNumber", function (v) {
    if ( isEmptyNoTrim(v) ) {
      return true;
    }
    v = parseNumber(v);
    return !isNaN(v) && v >= 0;

  }, mage.localize.translate('Please select one of the above options.'));

  jQuery.validator.addMethod("validateGreaterThanZero", function (v) {
    if ( isEmptyNoTrim(v) ) {
      return true;
    }
    v = parseNumber(v);
    return !isNaN(v) && v > 0;
  }, mage.localize.translate('Please enter a number greater than 0 in this field'));

  jQuery.validator.addMethod("validateCssLength", function (v) {
    if ( isEmptyNoTrim(v) ) {
      return true;
    }
    v = parseNumber(v);
    return !isNaN(v) && v > 0;
  }, mage.localize.translate("Please enter a number greater than 0 in this field"));
})();

jQuery.extend(jQuery.validator.messages, {
  required: mage.localize.translate("This is a required field."),
  remote: mage.localize.translate("Please fix this field."),
  email: mage.localize.translate("Please enter a valid email address."),
  url: mage.localize.translate("Please enter a valid URL."),
  date: mage.localize.translate("Please enter a valid date."),
  dateISO: mage.localize.translate("Please enter a valid date (ISO)."),
  number: mage.localize.translate("Please enter a valid number."),
  digits: mage.localize.translate("Please enter only digits."),
  creditcard: mage.localize.translate("Please enter a valid credit card number."),
  equalTo: mage.localize.translate("Please make sure your passwords match."),
  accept: mage.localize.translate("Please enter a value with a valid extension."),
  maxlength: $.validator.format(mage.localize.translate("Please enter no more than {0} characters.")),
  minlength: $.validator.format(mage.localize.translate("Please enter at least {0} characters.")),
  rangelength: $.validator.format(mage.localize.translate("Please enter a value between {0} and {1} characters long.")),
  range: $.validator.format(mage.localize.translate("Please enter a value between {0} and {1}.")),
  max: $.validator.format(mage.localize.translate("Please enter a value less than or equal to {0}.")),
  min: $.validator.format(mage.localize.translate("Please enter a value greater than or equal to {0}."))
});

// Setting the type as html5 to enable data-validate
$.metadata.setType("html5");

/*
 jQuery plugin for validator
 eg:$("#formId").mage().validate()
 */
(function ($) {
  $.fn.mage = function () {
    var jq = this;
    return {
      validate: function (options) {
        var defaultOptions = $.extend({
          meta: "validate",
          onfocusout: false,
          onkeyup: false,
          onclick: false,
          ignoreTitle: true,
          errorClass: 'mage-error',
          errorElement: 'div'
        }, options);
        return jq.each(function () {
          $(this).validate(defaultOptions);
          $(this).mageEventFormValidate();
        });
      }
    };
  };
})(jQuery);

/**
 Not implemented
 ====================
 validate-date-range
 validate-both-passwords
 validate-one-required
 validate-one-required-by-name
 validate-state
 validate-new-password
 validate-cc-number
 */

