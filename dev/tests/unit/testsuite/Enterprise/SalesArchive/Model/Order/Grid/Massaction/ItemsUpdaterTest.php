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

class Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdaterTest extends PHPUnit_Framework_TestCase
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
     * @var Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_updateArgs;

    protected function setUp()
    {
        $this->_cfgSalesArchive = $this->getMockBuilder('Enterprise_SalesArchive_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authorization = $this->getMockBuilder('Mage_Core_Model_Authorization')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_model = new Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater(
            array(
                'sales_archive_config' => $this->_cfgSalesArchive,
                'authModel' => $this->_authorization
            )
        );

        $this->_updateArgs = array(
            'add_order_to_archive' => array(
                'label' => 'Move to Archive',
                'url' => '*/sales_archive/massAdd'
            ),
            'cancel_order' => array(
                'label' => 'Cancel',
                'url' => '*/sales_archive/massCancel'
            )
        );
    }

    public function testConfigNotActive()
    {
        $this->_cfgSalesArchive->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(false));

        $this->assertEquals($this->_updateArgs, $this->_model->update($this->_updateArgs));
    }

    protected function _getAclResourceMap($isAllowed)
    {
        return array(
            array('Enterprise_SalesArchive::add', null, $isAllowed)
        );
    }

    protected function _getItemsId()
    {
        return array('add_order_to_archive');
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
