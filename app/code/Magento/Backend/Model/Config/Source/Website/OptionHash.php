<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Source\Website;

class OptionHash
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * System Store Model
     *
     * @var \Magento\Core\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Core\Model\System\Store
     */
    public function __construct(\Magento\Core\Model\System\Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getWebsiteOptionHash();
    }
}


