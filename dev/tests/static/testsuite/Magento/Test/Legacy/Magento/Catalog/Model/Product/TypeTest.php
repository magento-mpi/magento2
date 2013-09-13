<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for obsolete methods in Product Type instances
 */
class Magento_Test_Legacy_Magento_Catalog_Model_Product_TypeTest
    extends Magento_Test_Legacy_Magento_Catalog_Model_Product_AbstractTypeTest
{
    /**
     * @var array
     */
    protected $_productTypeFiles = array(
        '/app/code/Magento/Catalog/Model/Product/Type/Abstract.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Configurable.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Grouped.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Simple.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Virtual.php',
    );
}
