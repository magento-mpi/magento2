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
namespace Magento\Adminhtml\Block\Catalog\Product\Options;

class Ajax extends \Magento\Backend\Block\AbstractBlock
{
    /**
     * Return product custom options in JSON format
     *
     * @return string
     */
    protected function _toHtml()
    {
        $results = array();
        /** @var $optionsBlock \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Option */
        $optionsBlock = $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Option')
            ->setIgnoreCaching(true);

        $products = \Mage::registry('import_option_products');
        if (is_array($products)) {
            foreach ($products as $productId) {
                $product = \Mage::getModel('Magento\Catalog\Model\Product')->load((int)$productId);
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

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($output);
    }
}
