<?php
/**
 * Newsletter group options
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Block\Subscribe\Grid\Options;

class GroupOptionHash implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * System Store Model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Store\Model\System\Store $systemStore
     */
    public function __construct(\Magento\Store\Model\System\Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return store group array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getStoreGroupOptionHash();
    }
}
