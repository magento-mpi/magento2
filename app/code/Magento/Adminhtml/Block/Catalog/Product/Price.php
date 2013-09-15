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
 * Product price block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 */
namespace Magento\Adminhtml\Block\Catalog\Product;

class Price extends \Magento\Catalog\Block\Product\Price
{
    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($catalogData, $taxData, $coreData, $context, $registry, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * @param null|string|bool|int|\Magento\Core\Model\Store $storeId
     * @return bool|\Magento\Core\Model\Website
     */
    public function getWebsite($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getWebsite();
    }
}
