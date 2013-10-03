<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Compare Products Abstract Block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product\Compare;

abstract class AbstractCompare extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Catalog product compare
     *
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_catalogProductCompare = null;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Catalog\Helper\Product\Compare $catalogProductCompare
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Catalog\Helper\Product\Compare $catalogProductCompare,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_catalogProductCompare = $catalogProductCompare;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    /**
     * Retrieve Product Compare Helper
     *
     * @return \Magento\Catalog\Helper\Product\Compare
     */
    protected function _getHelper()
    {
        return $this->_catalogProductCompare;
    }

    /**
     * Retrieve Remove Item from Compare List URL
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getHelper()->getRemoveUrl($item);
    }
}
