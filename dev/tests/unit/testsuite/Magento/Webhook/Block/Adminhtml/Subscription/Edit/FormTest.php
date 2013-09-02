<?php
/**
 * Magento_Webhook_Block_Adminhtml_Subscription_Edit_Form
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_Subscription_Edit_FormTest extends Magento_Test_Block_Adminhtml
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_formMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_formFactoryMock;

    /** @var  Magento_Core_Model_Registry */
    private $_registry;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_formatMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_authenticationMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_hookMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_dataFormMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_fieldsetMock;

    /** @var  string[] */
    private $_actualIds;

    public function testPrepareColumns()
    {
        $this->_formFactoryMock = $this->_makeMock('Magento_Data_Form_Factory');
        $this->_registry = new Magento_Core_Model_Registry();
        $this->_formatMock = $this->_makeMock('Magento_Webhook_Model_Source_Format');
        $this->_authenticationMock = $this->_makeMock('Magento_Webhook_Model_Source_Authentication');
        $this->_hookMock = $this->_makeMock('Magento_Webhook_Model_Source_Hook');

        $selectMock = $this->_makeMock('Magento_DB_Select');
        $collectionMock = $this->_makeMock('Magento_Data_Collection_Db');
        $this->_setStub($collectionMock, 'getSelect', $selectMock);

        // Primary test logic
        $this->_dataFormMock = $this->_makeMock('Magento_Data_Form');
        $this->_setStub($this->_formFactoryMock, 'create', $this->_dataFormMock);
        $this->_fieldsetMock = $this->_makeMock('Magento_Data_Form_Element_Fieldset');
        $this->_setStub($this->_dataFormMock, 'addFieldset', $this->_fieldsetMock);
        $this->_fieldsetMock->expects($this->atLeastOnce())
            ->method('addField')
            ->will($this->returnCallback(array($this, 'logAddFieldArguments')));

        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);

        // Arguments passed to UUT's constructor
        $arguments = array(
            $this->_context,
            $coreStoreConfig,
            $this->_formFactoryMock,
            $this->_registry,
            $this->_formatMock,
            $this->_authenticationMock,
            $this->_hookMock,
            array($collectionMock)
        );

        // Parent methods, not being tested, to mock out
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

        $this->_formMock =  $this->getMock('Magento_Webhook_Block_Adminhtml_Subscription_Edit_Form', $methods,
            $arguments);
        $columnsSetMock = $this->_makeMock('Magento_Backend_Block_Widget_Grid_ColumnSet');
        $this->_setStub($this->_formMock, 'getChildBlock', $columnsSetMock);

        // Intended to call _prepareColumns
        $this->_formMock->toHtml();

        $expectedIds = array('name', 'endpoint_url', 'format', 'authentication_type', 'topics');
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
