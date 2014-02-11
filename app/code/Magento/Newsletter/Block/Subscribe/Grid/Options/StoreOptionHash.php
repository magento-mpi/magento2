<?php
/**
 * Newsletter store options
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Block\Subscribe\Grid\Options;

use Magento\Core\Model\System\Store;

class StoreOptionHash implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * System Store Model
     *
     * @var Store
     */
    protected $_systemStore;

    /**
     * @param Store $systemStore
     */
    public function __construct(Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return store array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getStoreOptionHash();
    }
}
