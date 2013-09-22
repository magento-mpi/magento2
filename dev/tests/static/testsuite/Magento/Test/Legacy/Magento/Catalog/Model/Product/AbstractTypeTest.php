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
 *
 * Abstract class is needed because it is not possible to run both tests of inherited class and its inheritors
 * @see https://github.com/sebastianbergmann/phpunit/issues/385
 */
abstract class Magento_Test_Legacy_\Magento\Catalog\Model\Product_AbstractTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_productTypeFiles = array();

    /**
     * @dataProvider obsoleteMethodsDataProvider
     *
     * @param string $method
     */
    public function testProductTypeModelsForObsoleteMethods($method)
    {
        $root = Magento_TestFramework_Utility_Files::init()->getPathToSource();
        foreach ($this->_productTypeFiles as $file) {
            $this->assertNotContains(
                '$this->' . $method . '(',
                file_get_contents($root . $file),
                "Method 'Magento\Catalog\Model\Product\Type\AbstractType::$method' is obsolete."
            );
        }
    }

    /**
     * @return array
     */
    public static function obsoleteMethodsDataProvider()
    {
        return array(
            array('getProduct'),
            array('setProduct'),
        );
    }
}
