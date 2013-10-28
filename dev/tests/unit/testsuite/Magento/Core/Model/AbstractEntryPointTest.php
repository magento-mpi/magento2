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

        $model = $this->getMockForAbstractClass(
            'Magento\Core\Model\AbstractEntryPoint',
            array(BP, array(), $objectManager),
            ''
        );
        $model->expects($this->once())
            ->method('_processRequest');
        $model->processRequest();
    }
}
