<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration item option model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Configuration_Item_Option extends Magento_Object
    implements Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
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
