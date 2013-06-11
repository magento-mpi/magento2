<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Category_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $createNum
     * @param int $totalCount
     * @param string|int $configuredCount
     * @param bool $expected
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($createNum, $totalCount, $configuredCount, $expected)
    {
        $resource = $this->getMock('Mage_Catalog_Model_Resource_Category', array(), array(), '', false);
        $resource->expects($this->any())->method('countVisible')->will($this->returnValue($totalCount));

        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $config->expects($this->once())->method('getNode')
            ->with(Saas_Limitation_Model_Catalog_Category_Limitation::XML_PATH_NUM_CATEGORIES)
            ->will($this->returnValue($configuredCount));

        $model = new Saas_Limitation_Model_Catalog_Category_Limitation($resource, $config);
        $this->assertEquals($expected, $model->isCreateRestricted($createNum));
    }

    /**
     * @return array
     */
    public function isCreateRestrictedDataProvider()
    {
        return array(
            'add 1 category with no limit'             => array(1, 0, '', false),
            'add 1 category with negative limit'       => array(1, 2, -1, false),
            'add 1 category with zero limit'           => array(1, 2, 0, false),
            'add 1 category with count > limit '       => array(1, 2, 1, true),
            'add 1 category with count = limit'        => array(1, 2, 2, true),
            'add 1 category with count < limit'        => array(1, 2, 3, false),
            'add 2 categories with count < limit'      => array(2, 2, 3, true),
            'add 2 categories with count much < limit' => array(2, 1, 3, false),
        );
    }
}
