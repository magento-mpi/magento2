<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class CronTest extends \PHPUnit_Framework_TestCase
{
    public function testCronActionFrontendAreaIsSetToDesignBeforeProcessOperation()
    {
        $designTheme = 'Magento/blank';

        $observer = $this->getMock(
            'Magento\ScheduledImportExport\Model\Observer',
            ['processScheduledOperation'],
            [],
            '',
            false
        );

        $theme = $this->getMock('Magento\Core\Model\Theme', [], [], '', false);

        $design = $this->getMock(
            'Magento\Core\Model\View\Design\Proxy',
            ['getArea', 'getDesignTheme', 'getConfigurationDesignTheme', 'setDesignTheme'],
            [],
            '',
            false
        );
        $design->expects($this->once())->method('getArea')
            ->will($this->returnValue('adminhtml'));
        $design->expects($this->once())->method('getDesignTheme')
            ->will($this->returnValue($theme));
        $design->expects($this->once())->method('getConfigurationDesignTheme')
            ->with($this->equalTo(\Magento\Framework\App\Area::AREA_FRONTEND))
            ->will($this->returnValue($designTheme));

        $design->expects($this->at(3))->method('setDesignTheme')
            ->with($this->equalTo($designTheme), $this->equalTo(\Magento\Framework\App\Area::AREA_FRONTEND));
        $design->expects($this->at(4))->method('setDesignTheme')
            ->with($this->equalTo($theme), $this->equalTo('adminhtml'));

        $response = $this->getMock('Magento\Framework\App\Console\Response', ['setRedirect'], [], '', false);

        $request = $this->getMock('Magento\Framework\App\Console\Request', ['getParam'], [], '', false);
        $request->expects($this->once())->method('getParam')
            ->with($this->equalTo('operation'))
            ->will($this->returnValue('2'));

        $objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager\ObjectManager',
            ['get'],
            [],
            '',
            false
        );
        $objectManagerMock->expects($this->at(0))->method('get')
            ->with($this->equalTo('Magento\Framework\View\DesignInterface'))
            ->will($this->returnValue($design));
        $objectManagerMock->expects($this->at(1))->method('get')
            ->with($this->equalTo('Magento\ScheduledImportExport\Model\Observer'))
            ->will($this->returnValue($observer));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            ['request' => $request, 'objectManager' => $objectManagerMock, 'response' => $response]
        );

        /** @var \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation $instance */
        $instance = $objectManager->getObject(
            'Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation\Cron',
            ['context' => $context]
        );
        $instance->execute();
    }
}
