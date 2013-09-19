<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Subscription\Edit\Form
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Subscription\Edit;

class FormTest extends \Magento\Test\Block\Adminhtml
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_formMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_formFactoryMock;

    /** @var  \Magento\Core\Model\Registry */
    private $_registry;

    /** @var  \Magento\Core\Helper\Data */
    protected $_coreData;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_formatMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_authenticationMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_hookMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_dataFormMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $_fieldsetMock;

    /** @var  string[] */
    private $_actualIds;

    public function testPrepareColumns()
    {
        $this->_formFactoryMock = $this->getMock('Magento\Data\Form\Factory', array('create'),
            array(), '', false, false);
        $this->_registry = new \Magento\Core\Model\Registry();
        $this->_coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->_formatMock = $this->_makeMock('Magento\Webhook\Model\Source\Format');
        $this->_authenticationMock = $this->_makeMock('Magento\Webhook\Model\Source\Authentication');
        $this->_hookMock = $this->_makeMock('Magento\Webhook\Model\Source\Hook');

        $selectMock = $this->_makeMock('Magento\DB\Select');
        $collectionMock = $this->_makeMock('Magento\Data\Collection\Db');
        $this->_setStub($collectionMock, 'getSelect', $selectMock);

        // Primary test logic
        $this->_dataFormMock = $this->_makeMock('Magento\Data\Form');
        $this->_setStub($this->_formFactoryMock, 'create', $this->_dataFormMock);
        $this->_fieldsetMock = $this->_makeMock('Magento\Data\Form\Element\Fieldset');
        $this->_setStub($this->_dataFormMock, 'addFieldset', $this->_fieldsetMock);
        $this->_fieldsetMock->expects($this->atLeastOnce())
            ->method('addField')
            ->will($this->returnCallback(array($this, 'logAddFieldArguments')));

        // Arguments passed to UUT's constructor
        $arguments = array(
            $this->_formFactoryMock,
            $this->_registry,
            $this->_coreData,
            $this->_context,
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

        $this->_formMock =  $this->getMock('Magento\Webhook\Block\Adminhtml\Subscription\Edit\Form', $methods,
            $arguments);
        $columnsSetMock = $this->_makeMock('Magento\Backend\Block\Widget\Grid\ColumnSet');
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
