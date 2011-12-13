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

class Legacy_ProductTypeModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider getSetProductDataProvider
     */
    public function testGetSetProduct($file)
    {
        $deprecations = array(
            'getProduct' => 'remove it',
            'setProduct' => 'remove it',
        );
        $content = file_get_contents($file);
        foreach ($deprecations as $method => $suggestion) {
            $this->assertNotContains(
                '$this->' . $method . '(',
                $content,
                "Deprecated method '$method' is used in the product type model, $suggestion."
            );
        }
    }

    public function getSetProductDataProvider()
    {
        $files = array(
            'app/code/core/Enterprise/GiftCard/Model/Catalog/Product/Type/Giftcard.php',
            'app/code/core/Mage/Bundle/Model/Product/Type.php',
            'app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
            'app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
            'app/code/core/Mage/Catalog/Model/Product/Type/Grouped.php',
            'app/code/core/Mage/Catalog/Model/Product/Type/Simple.php',
            'app/code/core/Mage/Catalog/Model/Product/Type/Virtual.php',
            'app/code/core/Mage/Downloadable/Model/Product/Type.php'
        );
        $result = array();
        foreach ($files as $file) {
            $file = PATH_TO_SOURCE_CODE . '/' . $file;
            /* Use filename as a data set name to not include it to every assertion message */
            $result[$file] = array($file);
        }
        return $result;
    }
}
