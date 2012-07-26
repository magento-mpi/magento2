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

class Mage_Adminhtml_Customer_GroupControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testNewAction()
    {
        $this->dispatch('backend/admin/customer_group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertStringMatchesFormat('%a<div class="content-header">%ANew Customer Group%a', $responseBody);
    }
}
