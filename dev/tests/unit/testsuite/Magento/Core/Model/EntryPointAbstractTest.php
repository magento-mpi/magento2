<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class EntryPointAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested: successful model creation, verification of directories, call of template method _processRequest()
     *
     * @magentoAppIsolation enabled
     */
    public function testProcessRequest()
    {
        $objectManager = $this->getMock('Magento\ObjectManager');

        $config = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);

        $model = $this->getMockForAbstractClass('Magento\Core\Model\EntryPointAbstract',
            array($config, $objectManager), '');
        $model->expects($this->once())
            ->method('_processRequest');
        $model->processRequest();
    }
}
