<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration item option model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Configuration_Item_Option extends Magento_Object
    implements Magento_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    /**
     * Returns value of this option
     * @return mixed
     */
    public function getValue()
    {
        return $this->_getData('value');
    }
}
