<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Rss
 */
class Mage_Rss_OrderControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testNewActionNonLoggedUser()
    {
        $this->markTestIncomplete('Incomplete until Mage_Core_Helper_Http stops exiting script for non-logged user');
        $this->dispatch('rss/order/new/');
    }

    /**
     * @magentoDataFixture adminUserFixture
     */
    public function testNewActionLoggedUser()
    {
        $admin = new Varien_Object(array('id' => 1));
        $session = Mage::getSingleton('Mage_Rss_Model_Session');
        $session->setAdmin($admin);

        $user = new Mage_Admin_Model_User;
        $user->loadByUsername('user');
        $adminSession = Mage::getSingleton('Mage_Admin_Model_Session');
        $adminSession->setUpdatedAt(time())
            ->setUser($user);

        $this->dispatch('rss/order/new/');

        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertFalse(($code >= 300) && ($code < 400));

        $xmlContentType = false;
        $headers = $response->getHeaders();
        foreach ($headers as $header) {
            if ($header['name'] != 'Content-Type') {
                continue;
            }
            if (strpos($header['value'], 'text/xml') !== false) {
                $xmlContentType = true;
            }
        }
        $this->assertTrue($xmlContentType, 'Rss document should output xml header');

        $body = $response->getBody();
        $this->assertContains('<rss', $body);
    }

    public static function adminUserFixture()
    {
        Mage_Admin_Utility_User::getInstance()
            ->createAdmin();
    }

    public static function adminUserFixtureRollback()
    {
        Mage_Admin_Utility_User::getInstance()
            ->destroyAdmin();
    }
}
