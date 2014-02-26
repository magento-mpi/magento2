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
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Options;

class Ajax extends \Magento\Backend\Block\AbstractBlock
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $registry;
        $this->_coreData = $coreData;
        $this->_productFactory = $productFactory;
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
        /** @var $optionsBlock \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option */
        $optionsBlock = $this->getLayout()
            ->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option')
            ->setIgnoreCaching(true);

        $products = $this->_coreRegistry->registry('import_option_products');
        if (is_array($products)) {
            foreach ($products as $productId) {
                $product = $this->_productFactory->create()->load((int)$productId);
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

        return $this->_jsonEncoder->encode($output);
    }
}
