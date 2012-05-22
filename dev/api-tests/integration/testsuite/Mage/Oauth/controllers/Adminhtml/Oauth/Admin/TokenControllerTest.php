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
class Mage_Oauth_Adminhtml_Oauth_Admin_TokenControllerTest extends Magento_Test_ControllerTestCaseAbstract
{
    /**
     * Get token data
     *
     * @return array
     */
    protected function _getFixtureModels()
    {
        return require realpath(dirname(__FILE__) . '/../../../..') .
                '/Model/_fixtures/tokenConsumerCreate.php';
    }

    /**
     * Test update revoke status
     *
     */
    public function testRevokeAction()
    {
        //generate test items
        $models = $this->_getFixtureModels();

        $redirectUrl  = 'admin/oauth_admin_token/index';

        $models = array_merge($models['token']['customer'], $models['token']['admin']);
        $tokenIds = array();
        /** @var $item Mage_Oauth_Model_Token */
        foreach ($models as $item) {
            $tokenIds[] = $item->getId();
        }

        $this->loginToAdmin();
        $this->getRequest()->setParam('items', $tokenIds);

        $message                = 'Token is not updated.';
        $messageMustNotUpdated  = 'Token is updated but it must be not.';

        foreach (array(0, 1) as $revoked) {
            $this->getRequest()->setParam('status', $revoked);
            Mage::unregister('application_params');
            $this->dispatch(Mage::getModel('Mage_Adminhtml_Model_Url')->getUrl('adminhtml/oauth_admin_token/revoke'));
            $this->assertRedirectMatch($redirectUrl);

            /** @var $item Mage_Oauth_Model_Token */
            foreach ($models as $item) {
                $mustChange = $item->getAdminId() && $item->getType() == Mage_Oauth_Model_Token::TYPE_ACCESS;
                $revokedTest = $mustChange ? $revoked : $item->getRevoked();
                $item->load($item->getId());
                $this->assertEquals($revokedTest, $item->getRevoked(), $mustChange ? $message : $messageMustNotUpdated);
            }
        }
    }


    /**
     * Test delete action
     */
    public function testDeleteAction()
    {
        //generate test items
        $models = $this->_getFixtureModels();

        $redirectUrl  = 'admin/oauth_admin_token/index';

        $models = array_merge($models['token']['customer'], $models['token']['admin']);
        $tokenIds = array();
        /** @var $item Mage_Oauth_Model_Token */
        foreach ($models as $item) {
            $tokenIds[] = $item->getId();
        }

        $this->loginToAdmin();
        $this->getRequest()->setParam('items', $tokenIds);

        $message                = 'Token is not deleted.';
        $messageMustNotUpdated  = 'Token is deleted but it must be not.';
        Mage::unregister('application_params');
        $this->dispatch(Mage::getModel('Mage_Adminhtml_Model_Url')->getUrl('adminhtml/oauth_admin_token/delete'));
        $this->assertRedirectMatch($redirectUrl);

        /** @var $item Mage_Oauth_Model_Token */
        foreach ($models as $item) {
            $mustChange = $item->getAdminId() && $item->getType() == Mage_Oauth_Model_Token::TYPE_ACCESS;
            $id = $item->getId();
            $item->setData(array());
            $item->load($id);
            $this->assertEquals(
                $item->getId(),
                $mustChange ? null : $id,
                $mustChange ? $message : $messageMustNotUpdated);
        }
    }
}
