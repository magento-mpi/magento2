<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test model admin Authorized Tokens controller
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_Oauth_Adminhtml_Oauth_AuthorizedTokensControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**#@+
     * Revoked status constants
     */
    const STATUS_REVOKED_FALSE = 0;
    const STATUS_REVOKED_TRUE  = 1;
    /**#@-*/

    /**
     * Get token data
     *
     * @return array
     */
    protected function _getFixtureModels()
    {
        return require realpath(dirname(__FILE__) . '/../../..') . '/Model/_fixture/_data/token_consumer_create.php';
    }

    /**
     * Test update revoke status
     *
     */
    public function testRevokeAction()
    {
        //generate test items
        $models      = $this->_getFixtureModels();
        $redirectUrl = 'admin/oauth_authorizedTokens/index';
        $models      = array_merge($models['token']['customer'], $models['token']['admin']);
        $tokenIds    = array();

        /** @var $item Mage_Oauth_Model_Token */
        foreach ($models as $item) {
            $tokenIds[] = $item->getId();
        }

        $this->loginToAdmin();
        $this->getRequest()->setParam('items', $tokenIds);

        // calls quantity accordingly to fixture
        $notificationsCount = array(self::STATUS_REVOKED_FALSE => 1, self::STATUS_REVOKED_TRUE => 2);

        foreach (array(self::STATUS_REVOKED_FALSE, self::STATUS_REVOKED_TRUE) as $revoked) {
            $this->getRequest()->setParam('status', $revoked);
            Mage::unregister('application_params');
            $this->_replaceHelperWithMock('Mage_Oauth_Helper_Data', array('sendNotificationOnTokenStatusChange'))
                 ->expects($this->exactly($notificationsCount[$revoked]))
                 ->method('sendNotificationOnTokenStatusChange');

            $this->dispatch($this->_getUrlPathWithSecretKey('adminhtml/oauth_authorizedTokens/revoke'));
            $this->assertRedirectMatch($redirectUrl);

            /** @var $item Mage_Oauth_Model_Token */
            foreach ($models as $item) {
                $mustChange = $item->getType() == Mage_Oauth_Model_Token::TYPE_ACCESS;
                $revokedTest = $mustChange ? $revoked : $item->getRevoked();
                $item->load($item->getId());
                $this->assertEquals(
                    $revokedTest,
                    $item->getRevoked(),
                    $mustChange ? 'Token is not updated.' : 'Token is updated but it must be not.'
                );
            }
        }
    }

    /**
     * Test delete action
     *
     */
    public function testDeleteAction()
    {
        //generate test items
        $models      = $this->_getFixtureModels();
        $redirectUrl = 'admin/oauth_authorizedTokens/index';
        $models      = array_merge($models['token']['customer'], $models['token']['admin']);
        $tokenIds    = array();

        /** @var $item Mage_Oauth_Model_Token */
        foreach ($models as $item) {
            $tokenIds[] = $item->getId();
        }

        $notifications = 2;

        $this->loginToAdmin();
        $this->getRequest()->setParam('items', $tokenIds);
        Mage::unregister('application_params');
        $this->_replaceHelperWithMock('Mage_Oauth_Helper_Data', array('sendNotificationOnTokenStatusChange'))
             ->expects($this->exactly($notifications))
             ->method('sendNotificationOnTokenStatusChange')
             ->will($this->returnValue(1));

        $this->dispatch($this->_getUrlPathWithSecretKey('adminhtml/oauth_authorizedTokens/delete'));
        $this->assertRedirectMatch($redirectUrl);

        /** @var $item Mage_Oauth_Model_Token */
        foreach ($models as $item) {
            $mustChange = $item->getType() == Mage_Oauth_Model_Token::TYPE_ACCESS;
            $id = $item->getId();
            $item->setData(array());
            $item->load($id);
            $this->assertEquals(
                $item->getId(),
                $mustChange ? null : $id,
                $mustChange ? 'Token is not deleted.' : 'Token is deleted but it must be not.');
        }
    }
}
