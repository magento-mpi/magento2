<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\Helper;

/**
 * Class AbstractHelperTest
 */
class AbstractHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\App\Helper\AbstractHelper */
    protected $helper;

    /** @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject */
    protected $moduleManagerMock;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlBuilderMock;

    protected function setUp()
    {
        $this->urlBuilderMock = $this->getMock('Magento\Framework\UrlInterface', [], [], '', false);
        $this->moduleManagerMock = $this->getMock('Magento\Framework\Module\Manager', [], [], '', false);
        $this->contextMock = $this->getMock('Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->contextMock->expects($this->once())
            ->method('getModuleManager')
            ->will($this->returnValue($this->moduleManagerMock));
        $this->contextMock->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilderMock));

        $this->helper = $this->getMockForAbstractClass(
            'Magento\Framework\App\Helper\AbstractHelper',
            ['context' => $this->contextMock]
        );
    }

    /**
     * @covers \Magento\Framework\App\Helper\AbstractHelper::isModuleEnabled
     * @covers \Magento\Framework\App\Helper\AbstractHelper::isModuleOutputEnabled
     * @param string|null $moduleName
     * @param string $requestedName
     * @param bool $result
     * @dataProvider isModuleEnabledDataProvider
     */
    public function testIsModuleEnabled($moduleName, $requestedName, $result)
    {
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with($this->equalTo($requestedName))
            ->will($this->returnValue($result));

        $this->moduleManagerMock->expects($this->once())
            ->method('isOutputEnabled')
            ->with($this->equalTo($requestedName))
            ->will($this->returnValue($result));
        $this->assertSame($result, $this->helper->isModuleEnabled($moduleName));
        $this->assertSame($result, $this->helper->isModuleOutputEnabled($moduleName));
    }

    /**
     * @return array
     */
    public function isModuleEnabledDataProvider()
    {
        return [
            [null, '', true],
            [null, '', false],
            ['Module_Name', 'Module_Name', false],
            ['Module\\Name', 'Module\\Name', true],
        ];
    }
}
