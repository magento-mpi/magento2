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
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new AreaEmulator($this->_objectManager);
    }

    public function testEmulateLayoutArea()
    {
        $configuration = array(
            'Magento\Core\Model\Layout' => array(
                'arguments' => array(
                    'area' => array(
                        \Magento\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE => 'string',
                        'value' => 'test_area'
                    )
                )
            )
        );
        $this->_objectManager->expects($this->once())->method('configure')->with($configuration);
        $this->_model->emulateLayoutArea('test_area');
    }
}
