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
                $shareable = (bool) Mage::getStoreConfigFlag(Magento_Downloadable_Model_Link::XML_PATH_CONFIG_IS_SHAREABLE);
        }
        return $shareable;
    }
}
