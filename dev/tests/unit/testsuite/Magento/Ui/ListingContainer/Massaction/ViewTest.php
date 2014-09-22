<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ListingContainer\Massaction;

use Magento\Framework\View\Element\Template;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class ViewTest
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateContext||\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $renderContextMock;

    /**
     * @var ContentTypeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeFactoryMock;

    /**
     * @var ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configFactoryMock;

    /**
     * @var ConfigBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configBuilderMock;

    /**
     * @var View
     */
    protected $view;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->contextMock = $this->getMock(
            'Magento\Framework\View\Element\Template\Context',
            [],
            [],
            '',
            false
        );
        $this->renderContextMock = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\Context',
            ['getNamespace', 'getStorage', 'getRequestParam'],
            [],
            '',
            false
        );
        $this->contentTypeFactoryMock = $this->getMock(
            'Magento\Ui\ContentType\ContentTypeFactory',
            [],
            [],
            '',
            false
        );
        $this->configFactoryMock = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\ConfigFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->configBuilderMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface',
            [],
            '',
            false
        );

        $this->view = new View(
            $this->contextMock,
            $this->renderContextMock,
            $this->contentTypeFactoryMock,
            $this->configFactoryMock,
            $this->configBuilderMock
        );
    }

    /**
     * Run test prepare method
     *
     * @return void
     */
    public function testPrepare()
    {
        /**
         * @var \Magento\Framework\View\Element\UiComponent\ConfigInterface
         * |\PHPUnit_Framework_MockObject_MockObject $configurationMock
         */
        $configurationMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ConfigInterface',
            [],
            '',
            false
        );
        /**
         * @var \Magento\Framework\View\Element\UiComponent\ConfigStorageInterface
         * |\PHPUnit_Framework_MockObject_MockObject $configStorageMock
         */
        $configStorageMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ConfigStorageInterface',
            ['addComponentsData', 'getDataCollection'],
            '',
            false
        );

        $this->renderContextMock->expects($this->at(0))
            ->method('getNamespace')
            ->will($this->returnValue('namespace'));
        $this->renderContextMock->expects($this->at(1))
            ->method('getNamespace')
            ->will($this->returnValue('namespace'));
        $this->configFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($configurationMock));

        $this->renderContextMock->expects($this->any())
            ->method('getStorage')
            ->will($this->returnValue($configStorageMock));

        $configStorageMock->expects($this->once())
            ->method('addComponentsData')
            ->with($configurationMock);

        $this->assertNull($this->view->prepare());
    }
}
