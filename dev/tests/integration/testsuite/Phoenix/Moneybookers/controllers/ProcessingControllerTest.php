<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Phoenix_Moneybookers
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Phoenix_Moneybookers_ProcessingControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testCancelActionRedirect()
    {
        $this->dispatch('moneybookers/processing/cancel');
        $redirectUrl = Mage::getUrl('checkout/cart');
        $this->assertContains(
            '<script type="text/javascript">parent.location.href="' . $redirectUrl . '";</script>',
            $this->getResponse()->getBody()
        );
    }
}
