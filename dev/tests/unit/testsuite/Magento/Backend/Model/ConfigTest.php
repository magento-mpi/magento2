<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_transFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataFactoryMock;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $this->_structureReaderMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Reader',
            array(),
            array(),
            '',
            false
        );
        $structureMock = $this->getMock('Magento\Backend\Model\Config\Structure', array(), array(), '', false);

        $this->_structureReaderMock->expects(
            $this->any()
        )->method(
            'getConfiguration'
        )->will(
            $this->returnValue($structureMock)
        );

        $this->_transFactoryMock = $this->getMock(
            'Magento\Core\Model\Resource\TransactionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_appConfigMock = $this->getMock('Magento\App\ConfigInterface', array(), array(), '', false);
        $this->_configLoaderMock = $this->getMock(
            'Magento\Backend\Model\Config\Loader',
            array('getConfigByPath'),
            array(),
            '',
            false
        );
        $this->_dataFactoryMock = $this->getMock(
            'Magento\Core\Model\Config\ValueFactory',
            array(),
            array(),
            '',
            false
        );

        $this->_storeManager = $this->getMockForAbstractClass('Magento\Core\Model\StoreManagerInterface');

        $this->_model = new \Magento\Backend\Model\Config(
            $this->_appConfigMock,
            $this->_eventManagerMock,
            $structureMock,
            $this->_transFactoryMock,
            $this->_configLoaderMock,
            $this->_dataFactoryMock,
            $this->_storeManager
        );
    }

    public function testSaveDoesNotDoAnythingIfGroupsAreNotPassed()
    {
        $this->_configLoaderMock->expects($this->never())->method('getConfigByPath');
        $this->_model->save();
    }

    public function testSaveEmptiesNonSetArguments()
    {
        $this->_structureReaderMock->expects($this->never())->method('getConfiguration');
        $this->assertNull($this->_model->getSection());
        $this->assertNull($this->_model->getWebsite());
        $this->assertNull($this->_model->getStore());
        $this->_model->save();
        $this->assertSame('', $this->_model->getSection());
        $this->assertSame('', $this->_model->getWebsite());
        $this->assertSame('', $this->_model->getStore());
    }

    public function testSaveToCheckAdminSystemConfigChangedSectionEvent()
    {
        $transactionMock = $this->getMock('Magento\Core\Model\Resource\Transaction', array(), array(), '', false);

        $this->_transFactoryMock->expects($this->any())->method('create')->will($this->returnValue($transactionMock));

        $this->_configLoaderMock->expects($this->any())->method('getConfigByPath')->will($this->returnValue(array()));

        $this->_eventManagerMock->expects(
            $this->at(1)
        )->method(
            'dispatch'
        )->with(
            $this->equalTo('admin_system_config_changed_section_'),
            $this->arrayHasKey('website')
        );

        $this->_eventManagerMock->expects(
            $this->at(1)
        )->method(
            'dispatch'
        )->with(
            $this->equalTo('admin_system_config_changed_section_'),
            $this->arrayHasKey('store')
        );

        $this->_model->setGroups(array('1' => array('data')));
        $this->_model->save();
    }
}
