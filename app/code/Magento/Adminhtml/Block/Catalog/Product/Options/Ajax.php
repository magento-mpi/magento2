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
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_coreData = $coreData;
        parent::__construct($context, $data);
    }

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

        $products = $this->_coreRegistry->registry('import_option_products');
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

        return $this->_coreData->jsonEncode($output);
    }
}
