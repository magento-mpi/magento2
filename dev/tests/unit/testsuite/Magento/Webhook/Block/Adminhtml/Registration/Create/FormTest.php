<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Registration\Create\Form
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
    private $_coreHelperMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_dataFormMock;

    /** @var  \Magento\Core\Model\Registry */
    private $_registry;

    /** @var  string[] */
    private $_actualIds;

    public function setUp()
    {
        parent::setUp();
        $this->_registry = new \Magento\Core\Model\Registry();
        $this->_coreHelperMock = $this->_makeMock('Magento\Core\Helper\Data');
        $this->_formFactoryMock = $this->_makeMock('Magento\Data\Form\Factory');

        $this->_dataFormMock = $this->_makeMock('Magento\Data\Form');
        $this->_setStub($this->_formFactoryMock, 'create', $this->_dataFormMock);

        $selectMock = $this->_makeMock('Magento\DB\Select');
        $collectionMock = $this->_makeMock('Magento\Data\Collection\Db');
        $this->_setStub($collectionMock, 'getSelect', $selectMock);

        $arguments = array(
            $this->_coreHelperMock,
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
            'Magento\Webhook\Block\Adminhtml\Registration\Create\Form',
            $methods,
            $arguments);
    }

    public function testPrepareColumns()
    {
        $columnsSetMock = $this->_makeMock('Magento\Backend\Block\Widget\Grid\ColumnSet');
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
