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
namespace Magento\Downloadable\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Check is link shareable or not
     *
     * @param \Magento\Downloadable\Model\Link | \Magento\Downloadable\Model\Link\Purchased\Item $link
     * @return bool
     */
    public function getIsShareable($link)
    {
        $shareable = false;
        switch ($link->getIsShareable()) {
            case \Magento\Downloadable\Model\Link::LINK_SHAREABLE_YES:
            case \Magento\Downloadable\Model\Link::LINK_SHAREABLE_NO:
                $shareable = (bool) $link->getIsShareable();
                break;
            case \Magento\Downloadable\Model\Link::LINK_SHAREABLE_CONFIG:
                $shareable = (bool) \Mage::getStoreConfigFlag(\Magento\Downloadable\Model\Link::XML_PATH_CONFIG_IS_SHAREABLE);
        }
        return $shareable;
    }
}
