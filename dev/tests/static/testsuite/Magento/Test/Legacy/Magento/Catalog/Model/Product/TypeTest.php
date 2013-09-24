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
namespace Magento\Test\Legacy\Magento\Catalog\Model\Product;

class TypeTest
    extends \Magento\Test\Legacy\Magento\Catalog\Model\Product\AbstractTypeTest
{
    /**
     * @var array
     */
    protected $_productTypeFiles = array(
        '/app/code/Magento/Catalog/Model/Product/Type/AbstractType.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Configurable.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Grouped.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Price.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Simple.php',
        '/app/code/Magento/Catalog/Model/Product/Type/Virtual.php',
    );
}
