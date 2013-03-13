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
class Legacy_ProductTypeModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider getSetProductDataProvider
     */
    public function testGetSetProduct($file)
    {
        $content = file_get_contents($file);
        foreach (array('getProduct', 'setProduct') as $method) {
            $this->assertNotContains(
                '$this->' . $method . '(',
                $content,
                "Method 'Mage_Catalog_Model_Product_Type_Abstract::$method' is obsolete."
            );
        }
    }

    public function getSetProductDataProvider()
    {
        $root = Utility_Files::init()->getPathToSource();
        $files = array(
            "$root/app/code/Enterprise/GiftCard/Model/Catalog/Product/Type/Giftcard.php",
            "$root/app/code/Mage/Bundle/Model/Product/Type.php",
            "$root/app/code/Mage/Catalog/Model/Product/Type/Abstract.php",
            "$root/app/code/Mage/Catalog/Model/Product/Type/Configurable.php",
            "$root/app/code/Mage/Catalog/Model/Product/Type/Grouped.php",
            "$root/app/code/Mage/Catalog/Model/Product/Type/Simple.php",
            "$root/app/code/Mage/Catalog/Model/Product/Type/Virtual.php",
            "$root/app/code/Mage/Downloadable/Model/Product/Type.php",
        );
        return Utility_Files::composeDataSets($files);
    }
}
