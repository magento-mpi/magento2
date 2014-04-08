<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab\Main */
    protected $main;

    /** @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $requestInterfaceMock;

    /** @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $layoutInterfaceMock;

    /** @var \Magento\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $managerInterfaceMock;

    /** @var \Magento\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlInterfaceMock;

    /** @var \Magento\TranslateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $translateInterfaceMock;

    /** @var \Magento\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheInterfaceMock;

    /** @var \Magento\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $designInterfaceMock;

    /** @var \Magento\Core\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $sessionMock;

    /** @var \Magento\Session\SidResolverInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $sidResolverInterfaceMock;

    /** @var \Magento\Core\Model\Store\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $configMock;

    /** @var \Magento\View\Url|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlMock;

    /** @var \Magento\View\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $configInterfaceMock;

    /** @var \Magento\App\Cache\StateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $stateInterfaceMock;

    /** @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject */
    protected $loggerMock;

    /** @var \Magento\Escaper|\PHPUnit_Framework_MockObject_MockObject */
    protected $escaperMock;

    /** @var \Magento\Filter\FilterManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterManagerMock;

    /** @var \Magento\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $timezoneInterfaceMock;

    /** @var \Magento\Translate\Inline\StateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $stateInterfaceMock2;

    /** @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /** @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $fileSystemMock;

    /** @var \Magento\View\TemplateEnginePool|\PHPUnit_Framework_MockObject_MockObject */
    protected $templateEnginePoolMock;

    /** @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $stateMock;

    /** @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerInterfaceMock;

    /** @var \Magento\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $authorizationInterfaceMock;

    /** @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $sessionMock2;

    /** @var \Magento\Math\Random|\PHPUnit_Framework_MockObject_MockObject */
    protected $randomMock;

    /** @var \Magento\Data\Form\FormKey|\PHPUnit_Framework_MockObject_MockObject */
    protected $formKeyMock;

    /** @var \Magento\Code\NameBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $nameBuilderMock;

    /** @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    /** @var \Magento\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $formFactoryMock;

    /** @var \Magento\Eav\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $eavHelperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $yesnoFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $inputtypeFactoryMock;

    /** @var \Magento\CustomAttributeManagement\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $customAttributeManagementHelperMock;

    /** @var \Magento\Rma\Helper\Eav|\PHPUnit_Framework_MockObject_MockObject */
    protected $rmaEavHelperMock;

    protected function setUp()
    {
        $this->requestInterfaceMock = $this->getMock(
            'Magento\App\RequestInterface',
            ['isSecure', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam']
        );
        $this->layoutInterfaceMock = $this->getMock('Magento\View\LayoutInterface');
        $this->managerInterfaceMock = $this->getMock('Magento\Event\ManagerInterface');
        $this->urlInterfaceMock = $this->getMock('Magento\UrlInterface');
        $this->translateInterfaceMock = $this->getMock('Magento\TranslateInterface');
        $this->cacheInterfaceMock = $this->getMock('Magento\App\CacheInterface');
        $this->designInterfaceMock = $this->getMock('Magento\View\DesignInterface');
        $this->sessionMock = $this->getMock('Magento\Core\Model\Session', [], [], '', false);
        $this->sidResolverInterfaceMock = $this->getMock('Magento\Session\SidResolverInterface');
        $this->configMock = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);
        $this->urlMock = $this->getMock('Magento\View\Url', [], [], '', false);
        $this->configInterfaceMock = $this->getMock('Magento\View\ConfigInterface');
        $this->stateInterfaceMock = $this->getMock('Magento\App\Cache\StateInterface');
        $this->loggerMock = $this->getMock('Magento\Logger', [], [], '', false);
        $this->escaperMock = $this->getMock('Magento\Escaper');
        $this->filterManagerMock = $this->getMock('Magento\Filter\FilterManager', [], [], '', false);
        $this->timezoneInterfaceMock = $this->getMock('Magento\Stdlib\DateTime\TimezoneInterface');
        $this->stateInterfaceMock2 = $this->getMock('Magento\Translate\Inline\StateInterface');
        $this->filesystemMock = $this->getMock('Magento\App\Filesystem', [], [], '', false);
        $this->fileSystemMock = $this->getMock('Magento\View\FileSystem', [], [], '', false);
        $this->templateEnginePoolMock = $this->getMock('Magento\View\TemplateEnginePool', [], [], '', false);
        $this->stateMock = $this->getMock('Magento\App\State', [], [], '', false);
        $this->storeManagerInterfaceMock = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $this->authorizationInterfaceMock = $this->getMock('Magento\AuthorizationInterface');
        $this->sessionMock2 = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->randomMock = $this->getMock('Magento\Math\Random');
        $this->formKeyMock = $this->getMock('Magento\Data\Form\FormKey', [], [], '', false);
        $this->nameBuilderMock = $this->getMock('Magento\Code\NameBuilder');

        $this->contextMock = $this->getMock(
            'Magento\Backend\Block\Template\Context',
            null,
            [
                'request' => $this->requestInterfaceMock,
                'layout' => $this->layoutInterfaceMock,
                'eventManager' => $this->managerInterfaceMock,
                'urlBuilder' => $this->urlInterfaceMock,
                'translator' => $this->translateInterfaceMock,
                'cache' => $this->cacheInterfaceMock,
                'design' => $this->designInterfaceMock,
                'session' => $this->sessionMock,
                'sidResolver' => $this->sidResolverInterfaceMock,
                'storeConfig' => $this->configMock,
                'viewUrl' => $this->urlMock,
                'viewConfig' => $this->configInterfaceMock,
                'cacheState' => $this->stateInterfaceMock,
                'logger' => $this->loggerMock,
                'escaper' => $this->escaperMock,
                'filterManager' => $this->filterManagerMock,
                'localeDate' => $this->timezoneInterfaceMock,
                'inlineTranslation' => $this->stateInterfaceMock2,
                'filesystem' => $this->filesystemMock,
                'viewFileSystem' => $this->fileSystemMock,
                'enginePool' => $this->templateEnginePoolMock,
                'appState' => $this->stateMock,
                'storeManager' => $this->storeManagerInterfaceMock,
                'authorization' => $this->authorizationInterfaceMock,
                'backendSession' => $this->sessionMock2,
                'mathRandom' => $this->randomMock,
                'formKey' => $this->formKeyMock,
                'nameBuilder' => $this->nameBuilderMock
            ]
        );

        $this->registryMock = $this->getMock('Magento\Registry');
        $this->formFactoryMock = $this->getMock('Magento\Data\FormFactory', [], [], '', false);
        $this->eavHelperMock = $this->getMock('Magento\Eav\Helper\Data', [], [], '', false);
        $this->yesnoFactoryMock = $this->getMock('Magento\Backend\Model\Config\Source\YesnoFactory', ['create']);
        $this->inputtypeFactoryMock = $this->getMock(
            'Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory',
            ['create']
        );
        $this->configMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Config', [], [], '', false);
        $this->customAttributeManagementHelperMock = $this->getMock(
            'Magento\CustomAttributeManagement\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->rmaEavHelperMock = $this->getMock('Magento\Rma\Helper\Eav', [], [], '', false);

        $this->main = (new ObjectManagerHelper($this))->getObject(
            'Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab\Main',
            [
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'formFactory' => $this->formFactoryMock,
                'eavData' => $this->eavHelperMock,
                'yesnoFactory' => $this->yesnoFactoryMock,
                'inputTypeFactory' => $this->inputtypeFactoryMock,
                'attributeConfig' => $this->configMock,
                'attributeHelper' => $this->customAttributeManagementHelperMock,
                'rmaEav' => $this->rmaEavHelperMock
            ]
        );
    }

    public function testUsedInFormsAndIsVisibleFieldsDependency()
    {
        $fieldset = $this->getMock('Magento\Data\Form\Element\Fieldset', [], [], '', false);
        $fieldset->expects($this->any())->method('addField')->will($this->returnSelf());
        $form = $this->getMock('Magento\Data\Form', ['addFieldset', 'getElement'], [], '', false);
        $form->expects($this->any())->method('addFieldset')->will($this->returnValue($fieldset));
        $form->expects($this->any())->method('getElement')->will($this->returnValue($fieldset));
        $this->formFactoryMock->expects($this->any())->method('create')->will($this->returnValue($form));

        $yesno = $this->getMock('Magento\Backend\Model\Config\Source\Yesno', [], [], '', false);
        $this->yesnoFactoryMock->expects($this->any())->method('create')->will($this->returnValue($yesno));

        $inputtype = $this->getMock('Magento\Backend\Model\Config\Source\Yesno', [], [], '', false);
        $this->inputtypeFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($inputtype));

        $this->customAttributeManagementHelperMock->expects($this->any())->method('getAttributeElementScopes')
            ->will($this->returnValue([]));

        $dependenceBlock = $this->getMock('Magento\Backend\Block\Widget\Form\Element\Dependence', [], [], '', false);
        $dependenceBlock->expects($this->any())->method('addFieldMap')->will($this->returnSelf());

        $this->layoutInterfaceMock->expects($this->once())->method('createBlock')
            ->with('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->will($this->returnValue($dependenceBlock));
        $this->layoutInterfaceMock->expects($this->any())->method('setChild')->with(null, null, 'form_after')
            ->will($this->returnSelf());

        $this->filesystemMock->expects($this->any())->method('getDirectoryRead')
            ->will($this->throwException(new \Exception('test')));

        $this->main->setAttributeObject(new \Magento\Object(['entity_type' => new \Magento\Object([])]));

        try {
            $this->main->toHtml();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('test', $e->getMessage());
        }
    }
}
