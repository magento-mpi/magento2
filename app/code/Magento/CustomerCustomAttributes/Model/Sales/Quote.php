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
 * Customer Quote model
 *
 * @method Magento_CustomerCustomAttributes_Model_Resource_Sales_Quote _getResource()
 * @method Magento_CustomerCustomAttributes_Model_Resource_Sales_Quote getResource()
 * @method Magento_CustomerCustomAttributes_Model_Sales_Quote setEntityId(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Model_Sales_Quote extends Magento_CustomerCustomAttributes_Model_Sales_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CustomerCustomAttributes_Model_Resource_Sales_Quote');
    }
}
