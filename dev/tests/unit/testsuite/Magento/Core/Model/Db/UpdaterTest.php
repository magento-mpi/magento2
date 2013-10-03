<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Db;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceResolverMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceSetupMock;

    /**
     * @var \Magento\Core\Model\Db\Updater
     */
    protected $_model;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento\Core\Model\Resource\SetupFactory', array(), array(), '', false);
        $this->_appStateMock = $this->getMock('Magento\Core\Model\App\State', array(), array(), '', false);
        $this->_resourceResolverMock = $this->getMock('Magento\Core\Model\Module\ResourceResolverInterface');
        $this->_moduleListMock = $this->getMock('Magento\Core\Model\ModuleListInterface');
        $this->_resourceSetupMock = $this->getMock('Magento\Catalog\Model\Resource\Setup', array(), array(), '', false);

        $moduleList = array('Test_Module' => array());
        $this->_moduleListMock->expects($this->any())
            ->method('getModules')
            ->will($this->returnValue($moduleList));

        $resourceList = array('catalog_setup');
        $this->_resourceResolverMock->expects($this->any())
            ->method('getResourceList')
            ->with('Test_Module')
            ->will($this->returnValue($resourceList));

        $createData = array(
            'resourceName' => 'catalog_setup',
            'moduleName' => 'Test_Module',
        );
        $this->_factoryMock->expects($this->any())
            ->method('create')
            ->with('Magento\Catalog\Model\Resource\Setup', $createData)
            ->will($this->returnValue($this->_resourceSetupMock));

        $this->_model = new \Magento\Core\Model\Db\Updater(
            $this->_factoryMock,
            $this->_appStateMock,
            $this->_moduleListMock,
            $this->_resourceResolverMock,
            array('catalog_setup' => 'Magento\Catalog\Model\Resource\Setup'),
            true
        );
    }

    /**
     * @covers \Magento\Core\Model\Db\Updater::updateScheme
     */
    public function testUpdateSchemeWithUpdateSkip()
    {
        $this->_appStateMock->expects($this->once())
            ->method('isInstalled')
            ->will($this->returnValue(true));

        $this->_appStateMock->expects($this->never())
            ->method('setUpdateMode');

        $this->_model->updateScheme();
    }

    /**
     * @covers \Magento\Core\Model\Db\Updater::updateScheme
     */
    public function testUpdateScheme()
    {
        $this->_appStateMock->expects($this->once())
            ->method('isInstalled')
            ->will($this->returnValue(false));

        $this->_appStateMock->expects($this->at(1))
            ->method('setUpdateMode')
            ->with(true);

        $this->_appStateMock->expects($this->at(2))
            ->method('setUpdateMode')
            ->with(false);

        $this->_resourceSetupMock->expects($this->once())
            ->method('applyUpdates');
        $this->_resourceSetupMock->expects($this->once())
            ->method('getCallAfterApplyAllUpdates')
            ->will($this->returnValue(true));
        $this->_resourceSetupMock->expects($this->once())
            ->method('afterApplyAllUpdates');

        $this->_model->updateScheme();
    }

    /**
     * @covers \Magento\Core\Model\Db\Updater::updateData
     */
    public function testUpdateData()
    {
        $this->_resourceSetupMock->expects($this->never())
            ->method('applyDataUpdates');

        $this->_model->updateData();
    }
}
