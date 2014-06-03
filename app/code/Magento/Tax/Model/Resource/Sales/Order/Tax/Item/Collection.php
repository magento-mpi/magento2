<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Sales\Order\Tax\Item;

/**
 * Order Tax Item Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Tax\Model\Sales\Order\Tax\Item', 'Magento\Tax\Model\Resource\Sales\Order\Tax\Item');
    }
}
