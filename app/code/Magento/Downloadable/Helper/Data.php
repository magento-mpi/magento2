<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable helper
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Check is link shareable or not
     *
     * @param Magento_Downloadable_Model_Link | Magento_Downloadable_Model_Link_Purchased_Item $link
     * @return bool
     */
    public function getIsShareable($link)
    {
        $shareable = false;
        switch ($link->getIsShareable()) {
            case Magento_Downloadable_Model_Link::LINK_SHAREABLE_YES:
            case Magento_Downloadable_Model_Link::LINK_SHAREABLE_NO:
                $shareable = (bool) $link->getIsShareable();
                break;
            case Magento_Downloadable_Model_Link::LINK_SHAREABLE_CONFIG:
                $shareable = (bool) $this->_coreStoreConfig->getConfigFlag(Magento_Downloadable_Model_Link::XML_PATH_CONFIG_IS_SHAREABLE);
        }
        return $shareable;
    }
}
