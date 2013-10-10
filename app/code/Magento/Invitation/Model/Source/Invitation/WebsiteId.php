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
     * Store
     *
     * @var \Magento\Core\Model\System\Store
     */
    protected $_store;

    /**
     * @param \Magento\Core\Model\System\Store $store
     */
    function __construct(
            \Magento\Core\Model\System\Store $store
    ) {
        $this->_store = $store;
    }

    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return  $this->_store->getWebsiteOptionHash();
    }
}
