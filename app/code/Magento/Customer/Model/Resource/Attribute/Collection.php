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
 * Customer EAV additional attribute resource collection
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Resource\Attribute;

class Collection extends \Magento\Eav\Model\Resource\Attribute\Collection
{
    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'customer';

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return $this->_entityTypeCode;
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('customer_eav_attribute_website');
    }
}
