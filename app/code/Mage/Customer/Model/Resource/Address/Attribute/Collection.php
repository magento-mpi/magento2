<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Address EAV additional attribute resource collection
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Address_Attribute_Collection
    extends Mage_Customer_Model_Resource_Attribute_Collection
{
    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $_entityTypeCode   = 'customer_address';
}
