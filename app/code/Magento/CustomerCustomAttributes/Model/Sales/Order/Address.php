<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

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
namespace Magento\CustomerCustomAttributes\Model\Sales\Order;

class Address extends \Magento\CustomerCustomAttributes\Model\Sales\Address\AbstractAddress
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerCustomAttributes\Model\Resource\Sales\Order\Address');
    }
}
