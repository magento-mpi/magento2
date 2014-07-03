<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    protected $model;

    /** @var \Magento\Backend\Model\Config\Structure|\PHPUnit_Framework_MockObject_MockObject */
    protected $configStructure;

    /** @var \Magento\Framework\App\Response\Http\FileFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $fileFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Backend\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $backendConfig;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    protected function setUp()
    {
        $this->configStructure = $this->getMock('Magento\Backend\Model\Config\Structure', [], [], '', false);
        $this->fileFactory = $this->getMock('Magento\Framework\App\Response\Http\FileFactory', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->backendConfig = $this->getMock('Magento\Backend\Model\Config', [], [], '', false);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Backend\Controller\Adminhtml\System\Config',
            [
                'configStructure' => $this->configStructure,
                'fileFactory' => $this->fileFactory,
                'storeManager' => $this->storeManager,
                'backendConfig' => $this->backendConfig,
                'request' => $this->request
            ]
        );
    }

    /**
     * @param string $scope
     * @dataProvider dispatchDataProvider
     * @expectedException \Magento\Framework\App\Action\NotFoundException
     */
    public function testDispatch($scope)
    {
        $request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $request->expects($this->any())
            ->method('getParam')
            ->with('section')
            ->will($this->returnValue('some_section'));
        $this->request->expects($this->any())
            ->method('getParam')
            ->will($this->returnCallback(function ($param) use ($scope) {
                return $scope == $param ? $scope . 'Id' : null;
            }));
        $section = $this->getMock('Magento\Backend\Model\Config\Structure\Element\Section', [], [], '', false);
        $section->expects($this->once())
            ->method('isAllowed')
            ->will($this->throwException(new \Zend_Acl_Exception()));
        $this->configStructure->expects($this->at(0))
            ->method('getElement')
            ->with('some_section')
            ->will($this->returnValue($section));
        if (in_array($scope, ['website', 'store'])) {
            $this->backendConfig->expects($this->any())
                ->method('__call')
                ->with('set' . ucfirst($scope), [$scope . 'Id']);
        }
        $this->model->dispatch($request);
    }

    public function dispatchDataProvider()
    {
        return [['store'], ['website'], ['default']];
    }
}
