<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_Storelauncher_Product_StateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $collectionSize
     * @param boolean $expectedResult
     * @dataProvider isTileCompleteDataProvider
     */
    public function testIsTileComplete($collectionSize, $expectedResult)
    {
        /** @var $productCollection Magento_Catalog_Model_Resource_Product_Collection */
        $productCollection = $this->getMock('Magento_Catalog_Model_Resource_Product_Collection',
            array('getSize'),
            array(),
            '',
            false
        );
        $productCollection->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($collectionSize));
        $stateResolver = new Saas_Launcher_Model_Storelauncher_Product_StateResolver($productCollection);

        // product tile is considered to be complete if at least one product has been created
        $this->assertEquals($expectedResult, $stateResolver->isTileComplete());
    }

    public function isTileCompleteDataProvider()
    {
        return array(
            array(1, true),
            array(0, false),
        );
    }
}
