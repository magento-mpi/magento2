<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation websites options source
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Model\Source\Invitation;

class WebsiteId implements \Magento\Core\Model\Option\ArrayInterface

{
    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return  \Mage::getSingleton('Magento\Core\Model\System\Store')->getWebsiteOptionHash();
    }
}
