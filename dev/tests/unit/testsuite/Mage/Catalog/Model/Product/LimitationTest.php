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
     * @param int $totalCount
     * @param string $configuredCount
     * @param bool $expected
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($totalCount, $configuredCount, $expected)
    {
        $resource = $this->getMock('Mage_Catalog_Model_Resource_Product', array('countAll'), array(), '', false);
        $resource->expects($this->any())->method('countAll')->will($this->returnValue($totalCount));

        $config = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $config->expects($this->once())->method('getNode')
            ->with(Mage_Catalog_Model_Product_Limitation::XML_PATH_NUM_PRODUCTS)
            ->will($this->returnValue($configuredCount));

        $model = new Mage_Catalog_Model_Product_Limitation($resource, $config);
        $this->assertEquals($expected, $model->isCreateRestricted());
    }

    /**
     * @return array
     */
    public function isCreateRestrictedDataProvider()
    {
        return array(
            'no limit'       => array(0, '', false),
            'negative limit' => array(2, -1, false),
            'zero limit'     => array(2, 0, false),
            'limit < count'  => array(2, 1, true),
            'limit = count'  => array(2, 2, true),
            'limit > count'  => array(2, 3, false),
        );
    }
}
