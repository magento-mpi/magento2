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

class GroupOptionHash implements \Magento\Core\Model\Option\ArrayInterface
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
     * Return store group array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getStoreGroupOptionHash();
    }
}
