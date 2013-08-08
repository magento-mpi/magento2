<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable helper
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Check is link shareable or not
     *
     * @param Mage_Downloadable_Model_Link | Mage_Downloadable_Model_Link_Purchased_Item $link
     * @return bool
     */
    public function getIsShareable($link)
    {
        $shareable = false;
        switch ($link->getIsShareable()) {
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_YES:
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_NO:
                $shareable = (bool) $link->getIsShareable();
                break;
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_CONFIG:
                $shareable = (bool) Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_CONFIG_IS_SHAREABLE);
        }
        return $shareable;
    }
}
