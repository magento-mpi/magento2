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
class Legacy_Mage_Catalog_Model_Product_TypeTest extends Legacy_Mage_Catalog_Model_Product_AbstractTypeTest
{
    /**
     * @var array
     */
    protected $_productTypeFiles = array(
        '/app/code/Mage/Catalog/Model/Product/Type/Abstract.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Configurable.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Grouped.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Simple.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Virtual.php',
    );
}
