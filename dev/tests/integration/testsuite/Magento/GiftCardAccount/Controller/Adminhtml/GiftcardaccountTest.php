<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class GiftcardaccountTest extends \Magento\Backend\Utility\Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/giftcardaccount/index');
        $this->assertContains(
            "Code Pool used: <b>100%</b>",
            $this->getResponse()->getBody()
        );
    }
}
