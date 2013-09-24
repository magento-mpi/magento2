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
namespace Magento\Test\Legacy\Magento\Downloadable\Model\Product;

class TypeTest
    extends \Magento\Test\Legacy\Magento\Catalog\Model\Product\AbstractTypeTest
{
    /**
     * @var array
     */
    protected $_productTypeFiles = array(
        '/app/code/Magento/Downloadable/Model/Product/Type.php',
    );
}
