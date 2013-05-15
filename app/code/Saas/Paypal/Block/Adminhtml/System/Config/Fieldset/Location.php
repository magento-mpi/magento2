<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset renderer for PayPal Merchant Location fieldset
 *
 * @author      Magento Saas Team <saas@magentocommerce.com>
 */
class Saas_Paypal_Block_Adminhtml_System_Config_Fieldset_Location
    extends Mage_Backend_Block_System_Config_Form_Fieldset
{
    /**
     * Add conflicts resolution js code to the fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getExtraJs($element)
    {
        $authMethodCredentials = Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_API_CREDENTIALS;
        $js = '
            document.observe("dom:loaded", function() {
                $$(".with-button button.button").each(function(configureButton) {
                    togglePaypalSolutionConfigureButton(configureButton, true);
                });
                var paypalConflictsObject = {
                    "isConflict": false,
                    "ecMissed": false,
                    "ecDependent": false,
                    sharePayflowEnabling: function(enabler, isEvent) {
                        paypalConflictsObject.payflowLinkEnabling(enabler, isEvent);
                        var ecPayflowEnabler = $$(".paypal-ec-payflow-enabler.fd-enabled")[0];
                        if (typeof ecPayflowEnabler == "undefined") {
                            return;
                        }
                        var ecPayflowScopeElement = adminSystemConfig.getScopeElement(ecPayflowEnabler);

                        if (!enabler.enablerObject.ecPayflow) {
                            if ((!ecPayflowScopeElement || !ecPayflowScopeElement.checked) && isEvent
                                && enabler.value == 1
                            ) {
                                ecPayflowEnabler.value = 0;
                                fireEvent(ecPayflowEnabler, "change");
                            }
                            return;
                        }

                        var enablerScopeElement = adminSystemConfig.getScopeElement(enabler);
                        if (enablerScopeElement && ecPayflowScopeElement
                            && enablerScopeElement.checked != ecPayflowScopeElement.checked
                            && (isEvent || ecPayflowScopeElement.checked)
                        ) {
                            $(ecPayflowScopeElement).click();
                        }

                        var ecEnabler = !enabler.enablerObject.ecBoarding ? $$(".paypal-ec-enabler.fd-enabled")[0]
                            : $$(".paypal-ec-boarding-enabler.fd-enabled")[0];
                        var ecOtherEnabler = enabler.enablerObject.ecBoarding ? $$(".paypal-ec-enabler.fd-enabled")[0]
                            : $$(".paypal-ec-boarding-enabler.fd-enabled")[0];
                        if (!isEvent && enabler.value == 1) {
                            paypalConflictsObject.ecDependent = ecPayflowEnabler;
                        }
                        if (ecPayflowEnabler.value != enabler.value && (isEvent || enabler.value == 1)
                            && !enabler.ecIndependent
                        ) {
                            ecPayflowEnabler.value = enabler.value;
                            fireEvent(ecPayflowEnabler, "change");
                            if (ecPayflowEnabler.value == 1) {
                                if (typeof ecEnabler != "undefined") {
                                    var ecEnablerScopeElement = adminSystemConfig.getScopeElement(ecEnabler);
                                    ecEnabler.disabled = false;
                                    ecEnabler.value = 1;
                                    if (typeof ecOtherEnabler != "undefined") {
                                        ecOtherEnabler.value = 0;
                                    }
                                    if (ecEnablerScopeElement && ecEnablerScopeElement.checked) {
                                        paypalConflictsObject.checklessEventAction(ecEnablerScopeElement, false);
                                    }
                                    paypalConflictsObject.checklessEventAction(ecEnabler, true);
                                    if (typeof ecOtherEnabler != "undefined") {
                                        paypalConflictsObject.checklessEventAction(ecOtherEnabler, true);
                                    }
                                }
                            }
                        }
                        if (!isEvent && ecPayflowEnabler.value == 1 && typeof ecEnabler != "undefined") {
                            var ecSolution = $$(".pp-method-express")[0];
                            if (typeof ecSolution != "undefined" && !$(ecSolution).hasClassName("enabled")) {
                                ecSolution.addClassName("enabled");
                            }
                        }
                    },
                    payflowLinkEnabling: function(enabler, isEvent) {
                        //Check PayPal Payflow Link enabler change only
                        if (isEvent && enabler.enablerObject.ecIndependent && !enabler.enablerObject.ecEnabler &&
                            !enabler.enablerObject.ecBoardingEnabler && !enabler.enablerObject.ecSeparate
                        ) {
                            var ecBoardingEnabler = $$(".paypal-ec-boarding-enabler.fd-enabled")[0];
                            if (enabler.value == 1 && ecBoardingEnabler.value == 1) {
                                ecBoardingEnabler.value = 0;
                                fireEvent(ecBoardingEnabler, "change");
                            } else if (enabler.value == 0) {
                                var ecEnabler = $$(".paypal-ec-enabler.fd-enabled")[0];
                                if ($(ecAuthenticationMethodField.id).value == 1 && ecEnabler.value == 1) {
                                    ecEnabler.value = 0;
                                    fireEvent(ecEnabler, "change");
                                }
                            }
                        }
                    },
                    onChangeEnabler: function(event) {
                        paypalConflictsObject.checkPaymentConflicts($(Event.element(event)), "change");
                    },
                    onClickEnablerScope: function(event) {
                        paypalConflictsObject.checkPaymentConflicts(
                            $(adminSystemConfig.getUpTr($(Event.element(event))).select(".paypal-enabler.fd-enabled")[0]),
                            "click"
                        );
                    },
                    getSharedElements: function(element) {
                        var sharedElements = [];
                        adminSystemConfig.mapClasses(element, true, function(elementClassName) {
                            $$("." + elementClassName).each(function(sharedElement) {
                                if (sharedElements.indexOf(sharedElement) == -1) {
                                    sharedElements.push(sharedElement);
                                }
                            });
                        });
                        if (sharedElements.length == 0) {
                            sharedElements.push(element);
                        }
                        return sharedElements;
                    },
                    checklessEventAction: function(element, isChange) {
                        var action = isChange ? "change" : "click";
                        var handler = isChange
                            ? paypalConflictsObject.onChangeEnabler
                            : paypalConflictsObject.onClickEnablerScope;
                        paypalConflictsObject.getSharedElements(element).each(function(sharedElement) {
                            Event.stopObserving(sharedElement, action, handler);
                            if (isChange) {
                                sharedElement.value = element.value;
                                if ($(sharedElement).requiresObj) {
                                    $(sharedElement).requiresObj.indicateEnabled();
                                }
                            }
                        });
                        if (isChange) {
                            fireEvent(element, "change");
                            var tdElement = adminSystemConfig.getUpTd(element);
                            if(typeof tdElement != "undefined" && tdElement.disabled) {
                                tdElement.disabled = false;
                            }
                        } else {
                            $(element).click();
                        }
                        paypalConflictsObject.getSharedElements(element).each(function(sharedElement) {
                            Event.observe(sharedElement, action, handler);
                        });
                    },
                    ecCheckAvailability: function() {
                        var ecButton = $$(".pp-method-express button.button")[0];
                        if (typeof ecButton == "undefined") {
                            return;
                        }
                        var couldBeConfigured = true;
                        $$(".paypal-enabler.fd-enabled").each(function(enabler) {
                            if (enabler.enablerObject.ecEnabler || enabler.enablerObject.ecBoardingEnabler
                                || enabler.enablerObject.ecConflicts || enabler.enablerObject.ecSeparate
                            ) {
                                return;
                            }
                            if (enabler.value == 1) {
                                couldBeConfigured = false;
                            }
                        });
                        if (couldBeConfigured) {
                            togglePaypalSolutionConfigureButton(ecButton, true);
                        } else {
                            togglePaypalSolutionConfigureButton(ecButton, false);
                        }
                    },
                    // type could be "initial", "change", "click"
                    checkPaymentConflicts: function(enabler, type) {
                        var isEvent = (type != "initial");
                        var ecEnabler = !enabler.enablerObject.ecBoarding ? $$(".paypal-ec-enabler.fd-enabled")[0]
                            : $$(".paypal-ec-boarding-enabler.fd-enabled")[0];
                        var ecOtherEnabler = enabler.enablerObject.ecBoarding ? $$(".paypal-ec-enabler.fd-enabled")[0]
                            : $$(".paypal-ec-boarding-enabler.fd-enabled")[0];
                        if (enabler.value == 0) {
                            if (!enabler.enablerObject.ecIndependent && type == "change") {
                                if (typeof ecEnabler != "undefined" && ecEnabler.value == 1) {
                                    var ecEnablerScopeElement = adminSystemConfig.getScopeElement(ecEnabler);
                                    if (!ecEnablerScopeElement || !ecEnablerScopeElement.checked) {
                                        ecEnabler.value = 0;
                                        paypalConflictsObject.checklessEventAction(ecEnabler, true);
                                    }
                                }
                            }
                            paypalConflictsObject.ecCheckAvailability();
                            paypalConflictsObject.sharePayflowEnabling(enabler, isEvent);
                            return;
                        }

                        var confirmationApproved = isEvent;
                        var confirmationShowed = false;
                        if (enabler.enablerObject.ecEnabler && !isEvent) {
                            var authField = $$(".ec-auth-method");
                            if (authField.length > 0) {
                                authField = authField[0];
                                if (authField.value == ' . $authMethodCredentials . ') {
                                    paypalConflictsObject.ecDependent = enabler;
                                }
                            }
                        }
                        // check other solutions
                        $$(".paypal-enabler.fd-enabled").each(function(anotherEnabler) {
                            var anotherEnablerScopeElement = adminSystemConfig.getScopeElement(anotherEnabler);
                            if (!confirmationApproved && isEvent || $(anotherEnabler) == enabler
                                || anotherEnabler.value == 0
                                && (!anotherEnablerScopeElement || !anotherEnablerScopeElement.checked)
                            ) {
                                return;
                            }
                            var conflict = enabler.enablerObject.ecConflicts && anotherEnabler.enablerObject.ecEnabler
                                || enabler.enablerObject.ecEnabler && anotherEnabler.enablerObject.ecConflicts

                                || enabler.enablerObject.ecConflicts && anotherEnabler.enablerObject.ecBoardingEnabler
                                || enabler.enablerObject.ecBoardingEnabler && anotherEnabler.enablerObject.ecConflicts

                                || !enabler.enablerObject.ecIndependent && anotherEnabler.enablerObject.ecConflicts

                                || !enabler.enablerObject.ecEnabler && !anotherEnabler.enablerObject.ecEnabler
                                    && !enabler.enablerObject.ecBoardingEnabler
                                    && !anotherEnabler.enablerObject.ecBoardingEnabler;

                            if (conflict && !confirmationShowed && anotherEnabler.value == 1) {
                                if (isEvent) {
                                    confirmationApproved = confirm(\'' .  $this->helper('Mage_Core_Helper_Data')->jsQuoteEscape($this->__('There is already another PayPal solution enabled. Enable this solution instead?')) . '\');
                                } else {
                                    paypalConflictsObject.isConflict = true;
                                }
                                confirmationShowed = true;
                            }
                            if (conflict && confirmationApproved) {
                                anotherEnabler.value = 0;
                                if (anotherEnablerScopeElement && anotherEnablerScopeElement.checked && isEvent) {
                                    paypalConflictsObject.checklessEventAction(anotherEnablerScopeElement, false);
                                }
                                paypalConflictsObject.checklessEventAction(anotherEnabler, true);
                            }
                        });

                        if (!enabler.enablerObject.ecIndependent) {
                            if (!isEvent && typeof ecEnabler != "undefined") {
                                paypalConflictsObject.ecDependent = ecEnabler;
                            }
                            if (!isEvent && (typeof ecEnabler == "undefined" || ecEnabler.value == 0)) {
                                if (!enabler.enablerObject.ecPayflow) {
                                    paypalConflictsObject.ecMissed = true;
                                }
                            } else if (isEvent && typeof ecEnabler != "undefined" && confirmationApproved) {
                                var ecEnablerScopeElement = adminSystemConfig.getScopeElement(ecEnabler);
                                if (ecEnablerScopeElement && ecEnablerScopeElement.checked) {
                                    paypalConflictsObject.checklessEventAction(ecEnablerScopeElement, false);
                                }
                                if (ecEnabler.value == 0) {
                                    ecEnabler.disabled = false;
                                    ecEnabler.value = 1;
                                    if (typeof ecOtherEnabler != "undefined") {
                                        ecOtherEnabler.value = 0;
                                    }
                                    paypalConflictsObject.checklessEventAction(ecEnabler, true);
                                    if (typeof ecOtherEnabler != "undefined") {
                                        paypalConflictsObject.checklessEventAction(ecOtherEnabler, true);
                                    }
                                }
                            }
                        }

                        if (!confirmationApproved && isEvent) {
                            enabler.value = 0;
                            paypalConflictsObject.checklessEventAction(enabler, true);
                        }
                        paypalConflictsObject.ecCheckAvailability();
                        paypalConflictsObject.sharePayflowEnabling(enabler, isEvent);
                    }
                };

                // fill enablers with conflict data
                $$(".paypal-enabler.fd-enabled").each(function(enablerElement) {
                    var enablerObj = {
                        ecIndependent: false,
                        ecConflicts: false,
                        ecEnabler: false,
                        ecBoardingEnabler: false,
                        ecBoarding: false,
                        ecSeparate: false,
                        ecPayflow: false
                    };
                    $(enablerElement).classNames().each(function(className) {
                        switch (className) {
                            case "paypal-ec-conflicts":
                                enablerObj.ecConflicts = true;
                            case "paypal-ec-independent":
                                enablerObj.ecIndependent = true;
                                break;
                            case "paypal-ec-enabler":
                                enablerObj.ecEnabler = true;
                                enablerObj.ecIndependent = true;
                                break;
                            case "paypal-ec-boarding-enabler":
                                enablerObj.ecBoardingEnabler = true;
                                enablerObj.ecIndependent = true;
                                break;
                            case "paypal-boarding-enabler":
                                enablerObj.ecBoarding = true;
                                break;
                            case "paypal-ec-separate":
                                enablerObj.ecSeparate = true;
                                enablerObj.ecIndependent = true;
                                break;
                            case "paypal-ec-pe":
                                enablerObj.ecPayflow = true;
                                break;
                        }
                    });
                    enablerElement.enablerObject = enablerObj;

                    Event.observe(enablerElement, "change", paypalConflictsObject.onChangeEnabler);
                    var enablerScopeElement = adminSystemConfig.getScopeElement(enablerElement);
                    if (enablerScopeElement) {
                        Event.observe(enablerScopeElement, "click", paypalConflictsObject.onClickEnablerScope);
                    }
                });

                var disablePayflow = true;
                $$(".paypal-ec-payflow-enabler.fd-enabled").each(function(payflowEnabler) {
                    var payflowScopeElement = adminSystemConfig.getScopeElement(payflowEnabler);
                    if ((typeof payflowScopeElement == "undefined" || !payflowScopeElement.checked)
                        && payflowEnabler.value == 1
                    ) {
                        disablePayflow = false;
                    }
                });

                if (disablePayflow) {
                    // initially uncheck payflow
                    var ecPayflowEnabler = $$(".paypal-ec-payflow-enabler.fd-enabled")[0];
                    if (typeof ecPayflowEnabler != "undefined") {
                        if (ecPayflowEnabler.value == 1) {
                            ecPayflowEnabler.value = 0;
                            fireEvent(ecPayflowEnabler, "change");
                        }

                        var ecPayflowScopeElement = adminSystemConfig.getScopeElement(ecPayflowEnabler);
                        if (ecPayflowScopeElement && !ecPayflowScopeElement.checked) {
                            $(ecPayflowScopeElement).click();
                        }
                    }
                }

                $$(".paypal-enabler.fd-enabled").each(function(enablerElement) {
                    paypalConflictsObject.checkPaymentConflicts(enablerElement, "initial");
                });
                if (paypalConflictsObject.isConflict || paypalConflictsObject.ecMissed) {
                    var notification = \'' .  $this->helper('Mage_Core_Helper_Data')->jsQuoteEscape($this->__('The following error(s) occured:')) . '\';
                    if (paypalConflictsObject.isConflict) {
                        notification += "\\n  " + \'' .  $this->helper('Mage_Core_Helper_Data')->jsQuoteEscape($this->__('Some PayPal solutions conflict.')) . '\';
                    }
                    if (paypalConflictsObject.ecMissed) {
                        notification += "\\n  " + \'' .  $this->helper('Mage_Core_Helper_Data')->jsQuoteEscape($this->__('PayPal Express Checkout is not enabled.')) . '\';
                    }
                    notification += "\\n" + \'' .  $this->helper('Mage_Core_Helper_Data')->jsQuoteEscape($this->__('Please re-enable the previously enabled payment solutions.')) . '\';
                    setTimeout(function() {
                        alert(notification);
                    }, 1);
                }

                $$(".requires").each(function(dependent) {
                    var $dependent = $(dependent);
                    if ($dependent.hasClassName("paypal-ec-enabler")
                        || $dependent.hasClassName("paypal-ec-boarding-enabler")
                        || $dependent.hasClassName("paypal-ec-payflow-enabler")
                    ) {
                        $dependent.requiresObj.callback = function(required) {
                            if ($(required).hasClassName("paypal-enabler") && required.value == 0) {
                                if (!paypalConflictsObject.ecDependent
                                    || paypalConflictsObject.getSharedElements($dependent)
                                        .indexOf(paypalConflictsObject.ecDependent) == -1
                                ) {
                                    $dependent.value = 0;
                                    fireEvent($dependent, "change");
                                }
                                $dependent.disable();
                            }
                        }
                        $dependent.requiresObj.requires.each(function(required) {
                            $dependent.requiresObj.callback(required);
                            paypalConflictsObject.ecDependent = false;
                        });
                    }
                });

                configForm.on(\'afterValidate\', function() {
                    var ecPayflowEnabler = $$(".paypal-ec-payflow-enabler.fd-enabled")[0];
                    if (typeof ecPayflowEnabler == "undefined") {
                        return;
                    }
                    var ecPayflowScopeElement = adminSystemConfig.getScopeElement(ecPayflowEnabler);
                    if ((typeof ecPayflowScopeElement == "undefined" || !ecPayflowScopeElement.checked)
                        && ecPayflowEnabler.value == 1
                    ) {
                        $$(".paypal-ec-enabler").each(function(ecEnabler) {
                            ecEnabler.value = 0;
                        });
                        $$(".paypal-ec-boarding-enabler.fd-enabled").each(function(ecEnabler) {
                            ecEnabler.value = 0;
                        });
                    }
                });
            });
        ';
        return parent::_getExtraJs($element)
            . $this->helper('Mage_Adminhtml_Helper_Js')->getScript($js);
    }
}
