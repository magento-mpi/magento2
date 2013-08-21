<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * JSON products custom options
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Options_Ajax extends Magento_Backend_Block_Abstract
{
    /**
     * Return product custom options in JSON format
     *
     * @return string
     */
    protected function _toHtml()
    {
        $results = array();
        /** @var $optionsBlock Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option */
        $optionsBlock = $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option')
            ->setIgnoreCaching(true);

        $products = Mage::registry('import_option_products');
        if (is_array($products)) {
            foreach ($products as $productId) {
                $product = Mage::getModel('Magento_Catalog_Model_Product')->load((int)$productId);
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

        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($output);
    }
}
