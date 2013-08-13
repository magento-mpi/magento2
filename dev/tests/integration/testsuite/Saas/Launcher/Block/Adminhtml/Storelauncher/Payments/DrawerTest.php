<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer
 *
 * @magentoAppArea adminhtml
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMoreUrlDataProvider
     */
    public function testGetMoreUrl($path, $url)
    {
        /** @var Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer $block */
        $block = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer');
        $this->assertEquals($url, $block->getMoreUrl($path));
    }

    public function getMoreUrlDataProvider()
    {
        return array(
            array(
                'payment/paypal_payments/wpp',
                'https://www.paypal.com/webapps/mpp/referral/website-payments-pro?partner_id=NB9WWHYEMVUMS'
            ),
        );
    }
}
