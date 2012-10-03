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
    protected $_cfgSalesArchive;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorization;

    /**
     * @var Enterprise_SalesArchive_Model_Order_Archive_Grid_Massaction_ItemsUpdater
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_updateArgs;

    public function setUp()
    {
        $this->_cfgSalesArchive = $this->getMockBuilder('Enterprise_SalesArchive_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authorization = $this->getMockBuilder('Mage_Core_Model_Authorization')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_model = new Enterprise_SalesArchive_Model_Order_Archive_Grid_Massaction_ItemsUpdater(
            array(
                'sales_archive_config' => $this->_cfgSalesArchive,
                'authModel' => $this->_authorization
            )
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

    public function tearDown()
    {
        unset($this->_model);
        unset($this->_authorization);
        unset($this->_cfgSalesArchive);
    }

    public function testConfigNotActive()
    {
        $this->_cfgSalesArchive->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(false));

        $this->assertEquals($this->_updateArgs, $this->_model->update($this->_updateArgs));
    }

    public function aclItemsDataProvider()
    {
        return array(
            array('cancel_order', 'Mage_Sales::cancel', 0),
            array('hold_order', 'Mage_Sales::hold', 1),
            array('unhold_order', 'Mage_Sales::unhold', 2),
            array('remove_order_from_archive', 'Enterprise_SalesArchive::remove', 3)
        );
    }

    protected function _getAclResourceMap($isAllowed)
    {
        return array(
            array('Mage_Sales::cancel', null, $isAllowed),
            array('Mage_Sales::hold', null, $isAllowed),
            array('Mage_Sales::unhold', null, $isAllowed),
            array('Enterprise_SalesArchive::remove', null, $isAllowed),
        );
    }

    protected function _getItemsId()
    {
        return array('cancel_order', 'hold_order', 'unhold_order', 'remove_order_from_archive');
    }

    public function testAuthAllowed()
    {
        $this->_cfgSalesArchive->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(true));

        $this->_authorization->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValueMap($this->_getAclResourceMap(true)));

        $updatedArgs = $this->_model->update($this->_updateArgs);
        var_dump($updatedArgs);
        foreach ($this->_getItemsId() as $massItemId) {
            $this->assertTrue(
                array_key_exists($massItemId, $updatedArgs)
            );
        }
    }

    public function testAuthNotAllowed()
    {
        $this->_cfgSalesArchive->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(true));

        $this->_authorization->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValueMap($this->_getAclResourceMap(false)));

        $updatedArgs = $this->_model->update($this->_updateArgs);
        foreach ($this->_getItemsId() as $massItemId) {
            $this->assertFalse(
                array_key_exists($massItemId, $updatedArgs)
            );
        }
    }

}
