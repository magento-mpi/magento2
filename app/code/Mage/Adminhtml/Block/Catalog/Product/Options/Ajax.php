<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * JSON products custom options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Options_Ajax extends Mage_Backend_Block_Abstract
{
    /**
     * Return product custom options in JSON format
     *
     * @return string
     */
    protected function _toHtml()
    {
        $results = array();
        /** @var $optionsBlock Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option */
        $optionsBlock = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option')
            ->setIgnoreCaching(true);

        $products = Mage::registry('import_option_products');
        if (is_array($products)) {
            foreach ($products as $productId) {
                $product = Mage::getModel('Mage_Catalog_Model_Product')->load((int)$productId);
                if (!$product->getId()) {
                    continue;
                }

                $optionsBlock->setProduct($product);
                $results = array_merge($results, $optionsBlock->getOptionValues());
            }
        }

        $output = array();
        foreach ($results as $resultObject) {
            $output[] = $resultObject->getData();
        }

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($output);
    }
}
