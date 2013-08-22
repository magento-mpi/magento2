<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PersistentHistory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise Persistent System Config Option Customer Segmentation admin frontend model
 *
 */
class Magento_PersistentHistory_Block_Adminhtml_System_Config_Customer extends Magento_Backend_Block_System_Config_Form_Field
{
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $elementId = $element->getHtmlId();
        $optionShoppingCartId = str_replace('/', '_', Magento_Persistent_Helper_Data::XML_PATH_PERSIST_SHOPPING_CART);
        $optionEnabled = str_replace('/', '_', Magento_Persistent_Helper_Data::XML_PATH_ENABLED);

        $addInheritCheckbox = false;
        if ($element->getCanUseWebsiteValue()) {
            $addInheritCheckbox = true;
        }
        elseif ($element->getCanUseDefaultValue()) {
            $addInheritCheckbox = true;
        }

        $html = '<script type="text/javascript">
            PersistentCustomerSegmentation = Class.create();
            PersistentCustomerSegmentation.prototype = {
                initialize : function () {
                    this._element = $("'.$elementId.'");
                    var funcTrackOnChangeShoppingCart = this.trackOnChangeShoppingCart.bind(this);
                    document.observe("dom:loaded", funcTrackOnChangeShoppingCart);
                    $("'.$optionShoppingCartId.'").observe("change", funcTrackOnChangeShoppingCart);
                    $("'.$optionEnabled.'").observe("change", function() {
                        setTimeout(funcTrackOnChangeShoppingCart, 1);
                    });'
                    .(($addInheritCheckbox)?
                        '$("'.$elementId.'_inherit").observe("change", funcTrackOnChangeShoppingCart);' : '')
                .'},

                disable: function() {
                    this._element.disabled = true;
                    this._element.value = 1;
                },

                enable: function() {
                    this._element.disabled = false;
                },

                trackOnChangeShoppingCart: function() {
                    if ($("'.$optionEnabled.'").value == 1 && $("'.$optionShoppingCartId.'").value == 1 ) {
                         this.disable();
                    } else {
                        '.(($addInheritCheckbox)? 'if ($("'.$elementId.'_inherit").checked) {
                            this.disable();
                        } else {
                            this.enable();
                        }' : 'this.enable();' ).'

                    }
                }
            };
        var persistentCustomerSegmentation = new PersistentCustomerSegmentation();
        </script>';

        return parent::render($element).$html;
    }
}
