<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTestCaseAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_modelName;

    /**
     * @var string
     */
    protected $_title;

    /**
     * @var array
     */
    protected $_testedMethods = array(
        'getTabLabel',
        'getTabTitle',
        'canShowTab',
        'isHidden',
        'getTabClass',
        'getAfter'
    );

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        $authorization = $this->getMock('Magento_AuthorizationInterface');
        $authorization->expects($this->any())
            ->method('isAllowed')
            ->with('Magento_Tag::tag_all')
            ->will($this->returnValue(true));

        $data = array(
            'authorization' => $authorization,
        );
        $this->_model = $objectManagerHelper->getObject($this->_modelName, $data);
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @return array
     */
    public function methodListDataProvider()
    {
        $methods = array();
        foreach ($this->_testedMethods as $method) {
            $methods['test for ' . $method] = array(
                '$method' => '_test' . ucfirst($method)
            );
        }

        return $methods;
    }

    protected function _testGetTabLabel()
    {
        $this->assertEquals($this->_title, $this->_model->getTabLabel());
    }

    protected function _testGetTabTitle()
    {
        $this->assertEquals($this->_title, $this->_model->getTabTitle());
    }

    protected function _testCanShowTab()
    {
        $this->assertTrue($this->_model->canShowTab());
    }

    protected function _testIsHidden()
    {
        $this->assertFalse($this->_model->isHidden());
    }

    protected function _testGetTabClass()
    {
        $this->assertEquals('ajax', $this->_model->getTabClass());
    }

    protected function _testGetAfter()
    {
        $this->assertEquals('product-reviews', $this->_model->getAfter());
    }
}
