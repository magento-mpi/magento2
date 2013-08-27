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
 * Tax Calculation Collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Resource_Calculation_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Tax_Model_Calculation', 'Magento_Tax_Model_Resource_Calculation');
    }
}
