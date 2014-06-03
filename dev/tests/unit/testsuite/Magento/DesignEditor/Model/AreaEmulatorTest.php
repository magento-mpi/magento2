<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model;

class AreaEmulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var AreaEmulator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\Framework\ObjectManager');
        $this->_model = new AreaEmulator($this->_objectManager);
    }

    public function testEmulateLayoutArea()
    {
        $configuration = array(
            'Magento\Framework\View\Layout' => array(
                'arguments' => array(
                    'area' => 'test_area'
                )
            )
        );
        $this->_objectManager->expects($this->once())->method('configure')->with($configuration);
        $this->_model->emulateLayoutArea('test_area');
    }
}
