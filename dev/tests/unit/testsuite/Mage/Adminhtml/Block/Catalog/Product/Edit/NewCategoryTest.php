<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_NewCategoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Adminhtml_Block_Catalog_Product_Edit_NewCategory */
    protected $_object;

    /** @var Mage_Core_Model_Url|PHPUnit_Framework_MockObject_MockObject */
    protected $_urlModel;

    protected function setUp()
    {
        $this->_urlModel = $this->getMock('Mage_Core_Model_Url', array('getUrl'));
        $this->_object = new Mage_Adminhtml_Block_Catalog_Product_Edit_NewCategory(array(
            'urlModel' => $this->_urlModel,
        ));
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_NewCategory::getSaveCategoryUrl
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_NewCategory::getSuggestCategoryUrl
     * @dataProvider urlMethodsDataProvider
     * @param string $expectedUrl
     * @param string $executedMethod
     */
    public function testGetUrlMethods($expectedUrl, $executedMethod)
    {
        $stringReverseCallback = function ($string)
        {
            return strrev($string);
        };
        $this->_urlModel->expects($this->once())
            ->method('getUrl')
            ->with($expectedUrl)
            ->will($this->returnCallback($stringReverseCallback));
        $this->assertEquals(
            strrev($expectedUrl),
            call_user_func_array(array($this->_object, $executedMethod), array($expectedUrl))
        );
    }

    /**
     * @return array
     */
    public static function urlMethodsDataProvider()
    {
        return array(
            array('*/catalog_category/save', 'getSaveCategoryUrl'),
            array('*/catalog_category/suggestCategories', 'getSuggestCategoryUrl'),
        );
    }
}
