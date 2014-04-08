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
    protected $requestInterface;

    /** @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $layoutInterface;

    /** @var \Magento\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $managerInterface;

    /** @var \Magento\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlInterface;

    /** @var \Magento\TranslateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $translateInterface;

    /** @var \Magento\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheInterface;

    /** @var \Magento\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $designInterface;

    /** @var \Magento\Core\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    /** @var \Magento\Session\SidResolverInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $sidResolverInterface;

    /** @var \Magento\Core\Model\Store\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $config;

    /** @var \Magento\View\Url|\PHPUnit_Framework_MockObject_MockObject */
    protected $url;

    /** @var \Magento\View\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $configInterface;

    /** @var \Magento\App\Cache\StateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheState;

    /** @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject */
    protected $logger;

    /** @var \Magento\Escaper|\PHPUnit_Framework_MockObject_MockObject */
    protected $escaper;

    /** @var \Magento\Filter\FilterManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterManager;

    /** @var \Magento\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $timezoneInterface;

    /** @var \Magento\Translate\Inline\StateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $translateState;

    /** @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $appFilesystem;

    /** @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $viewFilesystem;

    /** @var \Magento\View\TemplateEnginePool|\PHPUnit_Framework_MockObject_MockObject */
    protected $templateEnginePool;

    /** @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $appState;

    /** @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerInterface;

    /** @var \Magento\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $authorizationInterface;

    /** @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $backendSession;

    /** @var \Magento\Math\Random|\PHPUnit_Framework_MockObject_MockObject */
    protected $random;

    /** @var \Magento\Data\Form\FormKey|\PHPUnit_Framework_MockObject_MockObject */
    protected $formKey;

    /** @var \Magento\Code\NameBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $nameBuilder;

    /** @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    /** @var \Magento\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $formFactory;

    /** @var \Magento\Eav\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $eavHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $yesnoFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $inputtypeFactory;

    /** @var \Magento\CustomAttributeManagement\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $customAttributeManagementHelper;

    /** @var \Magento\Rma\Helper\Eav|\PHPUnit_Framework_MockObject_MockObject */
    protected $rmaEavHelper;

    protected function setUp()
    {
        $this->requestInterface = $this->getMock(
            'Magento\App\RequestInterface',
            ['isSecure', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam']
        );
        $this->layoutInterface = $this->getMock('Magento\View\LayoutInterface');
        $this->managerInterface = $this->getMock('Magento\Event\ManagerInterface');
        $this->urlInterface = $this->getMock('Magento\UrlInterface');
        $this->translateInterface = $this->getMock('Magento\TranslateInterface');
        $this->cacheInterface = $this->getMock('Magento\App\CacheInterface');
        $this->designInterface = $this->getMock('Magento\View\DesignInterface');
        $this->session = $this->getMock('Magento\Core\Model\Session', [], [], '', false);
        $this->sidResolverInterface = $this->getMock('Magento\Session\SidResolverInterface');
        $this->config = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);
        $this->url = $this->getMock('Magento\View\Url', [], [], '', false);
        $this->configInterface = $this->getMock('Magento\View\ConfigInterface');
        $this->cacheState = $this->getMock('Magento\App\Cache\StateInterface');
        $this->logger = $this->getMock('Magento\Logger', [], [], '', false);
        $this->escaper = $this->getMock('Magento\Escaper');
        $this->filterManager = $this->getMock('Magento\Filter\FilterManager', [], [], '', false);
        $this->timezoneInterface = $this->getMock('Magento\Stdlib\DateTime\TimezoneInterface');
        $this->translateState = $this->getMock('Magento\Translate\Inline\StateInterface');
        $this->appFilesystem = $this->getMock('Magento\App\Filesystem', [], [], '', false);
        $this->viewFilesystem = $this->getMock('Magento\View\FileSystem', [], [], '', false);
        $this->templateEnginePool = $this->getMock('Magento\View\TemplateEnginePool', [], [], '', false);
        $this->appState = $this->getMock('Magento\App\State', [], [], '', false);
        $this->storeManagerInterface = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $this->authorizationInterface = $this->getMock('Magento\AuthorizationInterface');
        $this->backendSession = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->random = $this->getMock('Magento\Math\Random');
        $this->formKey = $this->getMock('Magento\Data\Form\FormKey', [], [], '', false);
        $this->nameBuilder = $this->getMock('Magento\Code\NameBuilder');

        $this->context = $this->getMock(
            'Magento\Backend\Block\Template\Context',
            null,
            [
                'request' => $this->requestInterface,
                'layout' => $this->layoutInterface,
                'eventManager' => $this->managerInterface,
                'urlBuilder' => $this->urlInterface,
                'translator' => $this->translateInterface,
                'cache' => $this->cacheInterface,
                'design' => $this->designInterface,
                'session' => $this->session,
                'sidResolver' => $this->sidResolverInterface,
                'storeConfig' => $this->config,
                'viewUrl' => $this->url,
                'viewConfig' => $this->configInterface,
                'cacheState' => $this->cacheState,
                'logger' => $this->logger,
                'escaper' => $this->escaper,
                'filterManager' => $this->filterManager,
                'localeDate' => $this->timezoneInterface,
                'inlineTranslation' => $this->translateState,
                'filesystem' => $this->appFilesystem,
                'viewFileSystem' => $this->viewFilesystem,
                'enginePool' => $this->templateEnginePool,
                'appState' => $this->appState,
                'storeManager' => $this->storeManagerInterface,
                'authorization' => $this->authorizationInterface,
                'backendSession' => $this->backendSession,
                'mathRandom' => $this->random,
                'formKey' => $this->formKey,
                'nameBuilder' => $this->nameBuilder
            ]
        );

        $this->registry = $this->getMock('Magento\Registry');
        $this->formFactory = $this->getMock('Magento\Data\FormFactory', [], [], '', false);
        $this->eavHelper = $this->getMock('Magento\Eav\Helper\Data', [], [], '', false);
        $this->yesnoFactory = $this->getMock('Magento\Backend\Model\Config\Source\YesnoFactory', ['create']);
        $this->inputtypeFactory = $this->getMock(
            'Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory',
            ['create']
        );
        $this->config = $this->getMock('Magento\Eav\Model\Entity\Attribute\Config', [], [], '', false);
        $this->customAttributeManagementHelper = $this->getMock(
            'Magento\CustomAttributeManagement\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->rmaEavHelper = $this->getMock('Magento\Rma\Helper\Eav', [], [], '', false);

        $this->main = (new ObjectManagerHelper($this))->getObject(
            'Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab\Main',
            [
                'context' => $this->context,
                'registry' => $this->registry,
                'formFactory' => $this->formFactory,
                'eavData' => $this->eavHelper,
                'yesnoFactory' => $this->yesnoFactory,
                'inputTypeFactory' => $this->inputtypeFactory,
                'attributeConfig' => $this->config,
                'attributeHelper' => $this->customAttributeManagementHelper,
                'rmaEav' => $this->rmaEavHelper
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
        $this->formFactory->expects($this->any())->method('create')->will($this->returnValue($form));

        $yesno = $this->getMock('Magento\Backend\Model\Config\Source\Yesno', [], [], '', false);
        $this->yesnoFactory->expects($this->any())->method('create')->will($this->returnValue($yesno));

        $inputtype = $this->getMock('Magento\Backend\Model\Config\Source\Yesno', [], [], '', false);
        $this->inputtypeFactory->expects($this->any())->method('create')
            ->will($this->returnValue($inputtype));

        $this->customAttributeManagementHelper->expects($this->any())->method('getAttributeElementScopes')
            ->will($this->returnValue([]));

        $dependenceBlock = $this->getMock('Magento\Backend\Block\Widget\Form\Element\Dependence', [], [], '', false);
        $dependenceBlock->expects($this->any())->method('addFieldMap')->will($this->returnSelf());

        $this->layoutInterface->expects($this->once())->method('createBlock')
            ->with('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->will($this->returnValue($dependenceBlock));
        $this->layoutInterface->expects($this->any())->method('setChild')->with(null, null, 'form_after')
            ->will($this->returnSelf());

        $this->appFilesystem->expects($this->any())->method('getDirectoryRead')
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
