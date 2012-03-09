<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Sales_Order_CreateControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('admin/sales_order_create/loadBlock');
        $this->assertEquals('{"message":""}', $this->getResponse()->getBody());
    }
}
