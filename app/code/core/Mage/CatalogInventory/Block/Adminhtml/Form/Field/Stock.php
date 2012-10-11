<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * HTML select element block
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Block_Adminhtml_Form_Field_Stock extends Varien_Data_Form_Element_Select
{
    const QUANTITY_FIELD_HTML_ID = 'qty';

    /**
     * Quantity field element
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_qty;

    public function __construct(array $data = array())
    {
        $this->_qty = $data['qty'] ? : $this->_createQtyElement();
        unset($data['qty']);
        parent::__construct($data);
        $this->setName($data['name']);
    }

    /**
     * Create quantity field
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _createQtyElement()
    {
        $element = Mage::getModel('Varien_Data_Form_Element_Text');
        $element->setId(self::QUANTITY_FIELD_HTML_ID)->setName('qty');
        return $element;
    }

    /**
     * Join quantity and in stock elements' html
     *
     * @return string
     */
    public function getElementHtml()
    {
        return $this->_qty->getElementHtml() . parent::getElementHtml()
            . $this->_getJs(self::QUANTITY_FIELD_HTML_ID, $this->getId());
    }

    /**
     * Set form to quantity element in addition to current element
     *
     * @param $form
     * @return Varien_Data_Form_Element_Abstract
     */
    public function setForm($form)
    {
        $this->_qty->setForm($form);
        return parent::setForm($form);
    }

    /**
     * Set value to quantity element in addition to current element
     *
     * @param $value
     * @return mixed
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            $this->_qty->setValue($value['qty']);
        }
        return parent::setValue($value['is_in_stock']);
    }

    /**
     * Set name to quantity element in addition to current element
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_qty->setName($name . '[qty]');
        parent::setName($name . '[is_in_stock]');
    }

    protected function _getJs($quantityFieldId, $inStockFieldId)
    {
        return "
            <script>
            Event.observe(window, 'load', function() {
                (function ($) {
                    var qty = $('#$quantityFieldId');
                    var isInStock = $('#$inStockFieldId');
                    var disabler = function(){
                        if ('' === qty.val()) {
                            isInStock.attr('disabled', 'disabled');
                        } else {
                            isInStock.removeAttr('disabled');
                        }
                    };
                    disabler();
                    qty.bind('keyup change blur', disabler);

                    //Associated fields
                    var fieldsAssociations = {
                        '$quantityFieldId' : 'inventory_qty',
                        '$inStockFieldId'  : 'inventory_stock_availability'
                    };
                    //Fill corresponding field
                    var filler = function() {
                        var id = $(this).attr('id');
                        if ('undefined' !== typeof fieldsAssociations[id]) {
                            $('#' + fieldsAssociations[id]).val($(this).val());
                        } else {
                            $('#' + getKeyByValue(fieldsAssociations, id)).val($(this).val());
                        }
                    };
                    //Get key by value form object
                    var getKeyByValue = function(object, value) {
                        var returnVal = false;
                        $.each(object, function(objKey, objValue){
                            if (value === objValue) {
                                returnVal = objKey;
                            }
                        });
                        return returnVal;
                    };
                    $.each(fieldsAssociations, function(generalTabField, advancedTabField){
                        $('#' + generalTabField + ', #' + advancedTabField).bind('focus blur change keyup click', filler);
                        filler.call($('#' + generalTabField));
                        filler.call($('#' + advancedTabField));
                    });
                })(jQuery);
            });
            </script>
        ";
    }
}
