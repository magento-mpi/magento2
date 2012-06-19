<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test of visual design editor toolbar presence in "gift card account check" action
 */
class Enterprise_GiftCardAccount_DesignEditorTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testIndexStub()
    {
        $this->getRequest()->setParam('handle', 'enterprise_giftcardaccount_cart_quickcheck');
        $this->dispatch('design/editor/page');
        $this->assertContains('id="vde_toolbar"', $this->getResponse()->getBody());
    }
}
