<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Address EAV additional attribute resource collection
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Resource\Address\Attribute;

class Collection
    extends \Magento\Customer\Model\Resource\Attribute\Collection
{
    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $_entityTypeCode   = 'customer_address';
}
