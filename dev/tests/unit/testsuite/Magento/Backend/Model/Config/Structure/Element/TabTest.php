<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_TabTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Element\Tab
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_iteratorMock;

    protected function setUp()
    {
        $this->_iteratorMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Iterator\Field', array(), array(), '', false
        );
        $this->_applicationMock = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);

        $this->_model = new \Magento\Backend\Model\Config\Structure\Element\Tab(
            $this->_applicationMock, $this->_iteratorMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_iteratorMock);
        unset($this->_applicationMock);
    }

    public function testIsVisibleOnlyChecksPresenceOfChildren()
    {
        $this->_model->setData(array('showInStore' => 0, 'showInWebsite' => 0, 'showInDefault' => 0), 'store');
        $this->_iteratorMock->expects($this->once())->method('current')->will($this->returnValue(true));
        $this->_iteratorMock->expects($this->once())->method('valid')->will($this->returnValue(true));
        $this->assertTrue($this->_model->isVisible());
    }
}
