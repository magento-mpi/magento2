<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_SalesArchive
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_SalesArchive_Model_Order_Archive_Grid_Massaction_ItemsUpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cfgSalesArchiveMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var Enterprise_SalesArchive_Model_Order_Archive_Grid_Massaction_ItemsUpdater
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_updateArgs;

    protected function setUp()
    {
        $this->_cfgSalesArchiveMock = $this->getMockBuilder('Enterprise_SalesArchive_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authorizationMock = $this->getMockBuilder('Magento_AuthorizationInterface')
            ->getMock();

        $this->_model = new Enterprise_SalesArchive_Model_Order_Archive_Grid_Massaction_ItemsUpdater(
            $this->_cfgSalesArchiveMock,
            $this->_authorizationMock
        );

        $this->_updateArgs = array(
            'remove_order_from_archive' => array(
                'label' => 'Move to Orders Management',
                'url' => '*/sales_archive/massRemove'
            ),
            'cancel_order' => array(
                'label' => 'Cancel',
                'url' => '*/sales_archive/massCancel'
            ),
            'hold_order' => array(
                'label' => 'Hold',
                'url' => '*/sales_archive/massHold'
            ),
            'unhold_order' => array(
                'label' => 'Unhold',
                'url' => '*/sales_archive/massUnhold'
            ),
            'pdfinvoices_order' => array(
                'label' => 'Print Invoices',
                'url' => '*/sales_archive/massPrintInvoices'
            ),
            'pdfshipments_order' => array(
                'label' => 'Print Packing Slips',
                'url' => '*/sales_archive/massPrintPackingSlips'
            ),
            'pdfcreditmemos_order' => array(
                'label' => 'Print Credit Memos',
                'url' => '*/sales_archive/massPrintCreditMemos'
            ),
            'pdfdocs_order' => array(
                'label' => 'Print All',
                'url' => '*/sales_archive/massPrintAllDocuments'
            ),
            'print_shipping_label' => array(
                'label' => 'Print Shipping Labels',
                'url' => '*/sales_archive/massPrintShippingLabel'
            )
        );
    }

    public function testConfigNotActive()
    {
        $this->_cfgSalesArchiveMock->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(false));

        $this->assertEquals($this->_updateArgs, $this->_model->update($this->_updateArgs));
    }

    protected function _getAclResourceMap($isAllowed)
    {
        return array(
            array('Magento_Sales::cancel', null, $isAllowed),
            array('Magento_Sales::hold', null, $isAllowed),
            array('Magento_Sales::unhold', null, $isAllowed),
            array('Enterprise_SalesArchive::remove', null, $isAllowed),
        );
    }

    protected function _getItemsId()
    {
        return array('cancel_order', 'hold_order', 'unhold_order', 'remove_order_from_archive');
    }

    public function testAuthAllowed()
    {
        $this->_cfgSalesArchiveMock->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(true));

        $this->_authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValueMap($this->_getAclResourceMap(true)));

        $updatedArgs = $this->_model->update($this->_updateArgs);
        foreach ($this->_getItemsId() as $massItemId) {
            $this->assertArrayHasKey($massItemId, $updatedArgs);
        }
    }

    public function testAuthNotAllowed()
    {
        $this->_cfgSalesArchiveMock->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(true));

        $this->_authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValueMap($this->_getAclResourceMap(false)));

        $updatedArgs = $this->_model->update($this->_updateArgs);
        foreach ($this->_getItemsId() as $massItemId) {
            $this->assertArrayNotHasKey($massItemId, $updatedArgs);
        }
    }

}
