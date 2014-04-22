<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Model\Sales\Order;

/**
 * Customer Order Address model
 *
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Order\Address _getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Order\Address getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Sales\Order\Address setEntityId(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Address extends \Magento\CustomerCustomAttributes\Model\Sales\Address\AbstractAddress
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerCustomAttributes\Model\Resource\Sales\Order\Address');
    }
}
