<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PersistentHistory
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Block\Adminhtml\System\Config;

use Magento\Data\Form\Element\AbstractElement;

/**
 * Enterprise Persistent System Config Option Customer Segmentation admin frontend model
 *
 */
class Customer extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $elementId = $element->getHtmlId();
        $optionShoppingCartId = str_replace('/', '_', \Magento\Persistent\Helper\Data::XML_PATH_PERSIST_SHOPPING_CART);
        $optionEnabled = str_replace('/', '_', \Magento\Persistent\Helper\Data::XML_PATH_ENABLED);

        $addInheritCheckbox = false;
        if ($element->getCanUseWebsiteValue()) {
            $addInheritCheckbox = true;
        } elseif ($element->getCanUseDefaultValue()) {
            $addInheritCheckbox = true;
        }

        $html = '<script type="text/javascript">
            PersistentCustomerSegmentation = Class.create();
            PersistentCustomerSegmentation.prototype = {
                initialize : function () {
                    this._element = $("' .
            $elementId .
            '");
                    var funcTrackOnChangeShoppingCart = this.trackOnChangeShoppingCart.bind(this);
                    document.observe("dom:loaded", funcTrackOnChangeShoppingCart);
                    $("' .
            $optionShoppingCartId .
            '").observe("change", funcTrackOnChangeShoppingCart);
                    $("' .
            $optionEnabled .
            '").observe("change", function() {
                        setTimeout(funcTrackOnChangeShoppingCart, 1);
                    });' .
            ($addInheritCheckbox ? '$("' .
            $elementId .
            '_inherit").observe("change", funcTrackOnChangeShoppingCart);' : '') .
            '},

                disable: function() {
                    this._element.disabled = true;
                    this._element.value = 1;
                },

                enable: function() {
                    this._element.disabled = false;
                },

                trackOnChangeShoppingCart: function() {
                    if ($("' .
            $optionEnabled .
            '").value == 1 && $("' .
            $optionShoppingCartId .
            '").value == 1 ) {
                         this.disable();
                    } else {
                        ' .
            ($addInheritCheckbox ? 'if ($("' .
            $elementId .
            '_inherit").checked) {
                            this.disable();
                        } else {
                            this.enable();
                        }' : 'this.enable();') .
            '

                    }
                }
            };
        var persistentCustomerSegmentation = new PersistentCustomerSegmentation();
        </script>';

        return parent::render($element) . $html;
    }
}
