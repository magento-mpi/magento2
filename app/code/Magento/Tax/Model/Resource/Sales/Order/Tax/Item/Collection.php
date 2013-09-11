<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order Tax Item Collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Resource\Sales\Order\Tax\Item;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('\Magento\Tax\Model\Sales\Order\Tax\Item', '\Magento\Tax\Model\Resource\Sales\Order\Tax\Item');
    }
}
