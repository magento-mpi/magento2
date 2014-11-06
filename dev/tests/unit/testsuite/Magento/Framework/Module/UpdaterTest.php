<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceSetupMock;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\Module\Updater
     */
    protected $_model;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock(
            'Magento\Framework\Module\Updater\SetupFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_moduleListMock = $this->getMock('Magento\Framework\Module\ModuleListInterface');
        $this->_resourceResolver = $this->getMock('Magento\Framework\Module\ResourceResolverInterface');
        $this->_resourceSetupMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Setup',
            array(),
            array(),
            '',
            false
        );

        $moduleList = array('Test_Module' => array());
        $this->_moduleListMock->expects($this->any())->method('getModules')->will($this->returnValue($moduleList));

        $resourceList = array('catalog_setup');
        $this->_resourceResolver->expects($this->any())
            ->method('getResourceList')
            ->with('Test_Module')
            ->will($this->returnValue($resourceList))
        ;

        $this->moduleManager = $this->getMock('\Magento\Framework\Module\Manager', [], [], '', false);

        $resource = $this->getMock('\Magento\Framework\Module\ResourceInterface');

        $this->_model = new \Magento\Framework\Module\Updater(
            $this->_factoryMock,
            $this->_moduleListMock,
            $this->_resourceResolver,
            $this->moduleManager,
            $resource
        );
    }

    /**
     * @covers \Magento\Framework\Module\Updater::updateData
     */
    public function testUpdateDataNotApplied()
    {
        $this->moduleManager->expects($this->once())
            ->method('isDbDataUpToDate')
            ->with('Test_Module', 'catalog_setup')
            ->will($this->returnValue(true));
        $this->_factoryMock->expects($this->never())
            ->method('create');
        $this->_model->updateData();
    }

    public function testUpdateData()
    {
        $this->moduleManager->expects($this->once())
            ->method('isDbDataUpToDate')
            ->with('Test_Module', 'catalog_setup')
            ->will($this->returnValue(false));
        $this->_factoryMock->expects($this->any())
            ->method('create')
            ->with('catalog_setup', 'Test_Module')
            ->will($this->returnValue($this->_resourceSetupMock))
        ;
        $this->_resourceSetupMock->expects($this->once())
            ->method('applyDataUpdates');

        $this->_model->updateData();
    }

    public function testUpdateDataNoUpdates()
    {
        $this->moduleManager->expects($this->once())
            ->method('isDbDataUpToDate')
            ->with('Test_Module', 'catalog_setup')
            ->will($this->returnValue(true));
        $this->_factoryMock->expects($this->never())
            ->method('create');

        $this->_model->updateData();
    }
}
