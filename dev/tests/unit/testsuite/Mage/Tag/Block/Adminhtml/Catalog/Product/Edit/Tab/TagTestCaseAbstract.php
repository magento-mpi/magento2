<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTestCaseAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag
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
        $helperMock = $this->getMock('Mage_Tag_Helper_Data', array('__'), array(), '', false);
        $helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $authSession = $this->getMock('Mage_Backend_Model_Auth_Session', array('isAllowed'), array(), '', false);
        $authSession->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnCallback(array($this, 'isAllowedCallback')));

        $data = array(
            'helpers'      => array('Mage_Tag_Helper_Data' => $helperMock),
            'auth_session' => $authSession
        );

        $this->_model = new $this->_modelName($data);
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

    /**
     * @param string $data
     * @return bool
     */
    public function isAllowedCallback($data)
    {
        return $data == 'Mage_Tag::tag';
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
        $this->assertEquals('reviews', $this->_model->getAfter());
    }
}