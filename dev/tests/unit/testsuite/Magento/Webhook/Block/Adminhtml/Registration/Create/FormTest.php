<?php
/**
 * Magento_Webhook_Block_Adminhtml_Registration_Create_Form
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_Registration_Create_FormTest extends Magento_Test_Block_Adminhtml
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_formMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_formFactoryMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_coreData;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_dataFormMock;

    /** @var  Magento_Core_Model_Registry */
    private $_registry;

    /** @var  string[] */
    private $_actualIds;

    public function setUp()
    {
        parent::setUp();
        $this->_registry = new Magento_Core_Model_Registry();
        $this->_coreData = $this->_makeMock('Magento_Core_Helper_Data');
        $this->_formFactoryMock = $this->getMock('Magento_Data_Form_Factory', array('create'),
            array(), '', false, false);

        $this->_dataFormMock = $this->_makeMock('Magento_Data_Form');
        $this->_setStub($this->_formFactoryMock, 'create', $this->_dataFormMock);

        $selectMock = $this->_makeMock('Magento_DB_Select');
        $collectionMock = $this->_makeMock('Magento_Data_Collection_Db');
        $this->_setStub($collectionMock, 'getSelect', $selectMock);

        $arguments = array(
            $this->_coreData,
            $this->_registry,
            $this->_context,
            $this->_formFactoryMock
        );

        $methods = array(
            'getId',
            'sortColumnsByOrder',
            '_prepareMassactionBlock',
            '_prepareFilterButtons',
            'getChildBlock',
            '_toHtml',
            '_saveCache',
            '_afterToHtml',
            'addColumn'

        );

        $this->_formMock = $this->getMock(
            'Magento_Webhook_Block_Adminhtml_Registration_Create_Form',
            $methods,
            $arguments);
    }

    public function testPrepareColumns()
    {
        $columnsSetMock = $this->_makeMock('Magento_Backend_Block_Widget_Grid_ColumnSet');
        $this->_setStub($this->_formMock, 'getChildBlock', $columnsSetMock);

        $this->_dataFormMock->expects($this->exactly(4))
            ->method('addField')
            ->will($this->returnCallback(array($this, 'logAddFieldArguments')));

        // Intended to call _prepareColumns
        $this->_formMock->toHtml();

        $expectedIds = array('company', 'email', 'apikey', 'apisecret');
        $this->assertEquals($expectedIds, $this->_actualIds);
    }

    /**
     * Logs addField's id argument for later verification
     *
     * @param string $actualId
     */
    public function logAddFieldArguments($actualId)
    {
        $this->_actualIds[] = $actualId;
    }
}
