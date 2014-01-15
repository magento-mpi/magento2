<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Persistent\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \Magento\Core\Model\Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesReader;

    /**
     * @var  \Magento\Persistent\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_modulesReader = $this->getMock(
            '\Magento\Module\Dir\Reader',
            array(),
            array(),
            '',
            false
        );
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $objectManager->getObject('\Magento\Persistent\Helper\Data', array(
            'modulesReader' => $this->_modulesReader,
        ));
    }

    public function testGetPersistentConfigFilePath()
    {
        $this->_modulesReader->expects($this->once())->method('getModuleDir')
            ->with('etc', 'Magento_Persistent')
            ->will($this->returnValue('path123'));
        $this->assertEquals('path123/persistent.xml', $this->_helper->getPersistentConfigFilePath());
    }
}
