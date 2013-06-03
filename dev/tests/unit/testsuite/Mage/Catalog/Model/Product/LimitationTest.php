<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Model_Product_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $createNum
     * @param int $totalCount
     * @param string|int $configuredCount
     * @param bool $expected
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($createNum, $configuredCount, $totalCount, $expected)
    {
        $model = $this->_buildModel($configuredCount, $totalCount);
        $this->assertEquals($expected, $model->isCreateRestricted($createNum));
    }

    /**
     * Build a model to be tested
     *
     * @param int $totalCount
     * @param int $configuredCount
     * @return Mage_Catalog_Model_Product_Limitation
     */
    protected function _buildModel($configuredCount, $totalCount = null)
    {
        $resource = $this->getMock('Mage_Catalog_Model_Resource_Product', array('countAll'), array(), '', false);
        $resource->expects($this->any())->method('countAll')->will($this->returnValue($totalCount));

        $config = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $config->expects($this->once())->method('getNode')
            ->with(Mage_Catalog_Model_Product_Limitation::XML_PATH_NUM_PRODUCTS)
            ->will($this->returnValue($configuredCount));

        return new Mage_Catalog_Model_Product_Limitation($resource, $config);
    }

    /**
     * @return array
     */
    public function isCreateRestrictedDataProvider()
    {
        return array(
            'add 1 product with no limit'            => array(1, '', 0, false),
            'add 1 product with negative limit'      => array(1, -1, 2, false),
            'add 1 product with zero limit'          => array(1, 0, 2, false),
            'add 1 product with count > limit '      => array(1, 1, 2, true),
            'add 1 product with count = limit'       => array(1, 2, 2, true),
            'add 1 product with count < limit'       => array(1, 3, 2, false),
            'add 2 products with count < limit'      => array(2, 3, 2, true),
            'add 2 products with count much < limit' => array(2, 3, 1, false),
        );
    }

    public function testGetLimit()
    {
        $model = $this->_buildModel(5);
        $this->assertEquals(5, $model->getLimit());
    }
}
