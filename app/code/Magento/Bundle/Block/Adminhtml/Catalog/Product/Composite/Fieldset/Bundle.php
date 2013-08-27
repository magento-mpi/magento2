<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for fieldset of bundle product
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Bundle
    extends Magento_Bundle_Block_Catalog_Product_View_Type_Bundle
{
    public function __construct(Magento_Catalog_Helper_Product $catalogProduct, Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($catalogProduct, $context, $data);
    }

    /**
     * Returns string with json config for bundle product
     *
     * @return string
     */
    public function getJsonConfig() {
        $options = array();
        $optionsArray = $this->getOptions();
        foreach ($optionsArray as $option) {
            $optionId = $option->getId();
            $options[$optionId] = array('id' => $optionId, 'selections' => array());
            foreach ($option->getSelections() as $selection) {
                $options[$optionId]['selections'][$selection->getSelectionId()] = array(
                    'can_change_qty' => $selection->getSelectionCanChangeQty(),
                    'default_qty'    => $selection->getSelectionQty()
                );
            }
        }
        $config = array('options' => $options);
        return $this->_coreData->jsonEncode($config);
    }
}
