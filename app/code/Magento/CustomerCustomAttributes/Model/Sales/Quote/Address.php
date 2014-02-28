<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Model\Sales\Quote;

/**
 * Customer Quote Address model
 *
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote\Address _getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote\Address getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address setEntityId(int $value)
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
        $this->_init('Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote\Address');
    }
}
