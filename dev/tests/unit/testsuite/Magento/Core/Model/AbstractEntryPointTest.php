<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class AbstractEntryPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested: successful model creation, verification of directories, call of template method _processRequest()
     *
     * @magentoAppIsolation enabled
     */
    public function testProcessRequest()
    {
        $objectManager = $this->getMock('Magento\ObjectManager');

        $handler = $this->getMock('Magento\Error\HandlerInterface', array(), array(), '', false);
        $objectManager->expects($this->once())->method('create')->will($this->returnValue($handler));

        $config = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);
        /** @var $model \Magento\Core\Model\AbstractEntryPoint */
        $model = $this->getMockForAbstractClass('Magento\Core\Model\AbstractEntryPoint',
            array($config, $objectManager), '');
        $model->expects($this->once())
            ->method('_processRequest');
        $model->processRequest();
    }
}
