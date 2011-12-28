<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test model customer My Applications controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_OAuth_MyApplicationControllerTest extends Magento_Test_ControllerTestCaseAbstract
{
    /**
     * Test update revoke status
     */
    public function testAccess()
    {
        //check index
        $redirectUrl = 'customer/account/login';
        $this->dispatch('oauth/MyApplication/index');
        $this->assertRedirectMatch($redirectUrl);

        //check revoke
        Mage::unregister('application_params');
        $this->dispatch('oauth/MyApplication/revoke');
        $this->assertRedirectMatch($redirectUrl);

        //check delete
        Mage::unregister('application_params');
        $this->dispatch('oauth/MyApplication/delete');
        $this->assertRedirectMatch($redirectUrl);
    }

    /**
     * Test update revoke status action
     */
    public function testRevokeStatusUpdate()
    {
        $this->loginToFrontend();
        //generate test item
        $models = require realpath(dirname(__FILE__) . '/..')
                            . '/Model/_fixtures/tokenConsumerCreate.php';

        $models = array(
            $models['token']['admin'][0], //token which not related to customer, revoked = 0
            $models['token']['admin'][1], //token which not related to customer, revoked = 1
            $models['token']['customer'][0], //revoked = 0
            $models['token']['customer'][1], //revoked = 1
        );

        $message = 'Token has wrong revoked value.';
        $messageNonCustomer = 'Token is updated but it must be not.';

        $redirectUrl = 'oauth/myApplication/index';

        /** @var $token Mage_OAuth_Model_Token */
        foreach ($models as $token) {
            $revoked = (int) !$token->getRevoked();
            $revokedOld = $token->getRevoked();
            $this->getRequest()->setParam('id', $token->getId());
            $this->getRequest()->setParam('status', $revoked);
            Mage::unregister('application_params');
            $this->dispatch('oauth/myApplication/revoke');
            $this->assertRedirectMatch($redirectUrl);
            $token->load($token->getId());
            $this->assertEquals(
                $token->getCustomerId() ? $revoked : $revokedOld,
                $token->getRevoked(),
                $token->getCustomerId() ? $message : $messageNonCustomer);
        }
    }

    /**
     * Test delete action
     */
    public function testDelete()
    {
        $this->loginToFrontend();
        //generate test item
        $models = require realpath(dirname(__FILE__) . '/..')
                            . '/Model/_fixtures/tokenConsumerCreate.php';

        $models = array(
            $models['token']['admin'][0], //token which not related to customer, revoked = 0
            $models['token']['admin'][1], //token which not related to customer, revoked = 1
            $models['token']['customer'][0], //revoked = 0
            $models['token']['customer'][1], //revoked = 1
        );

        $message = 'Token cannot deleted.';
        $messageNonCustomer = 'Token is deleted but it must be not.';

        $redirectUrl = 'oauth/myApplication/index';

        /** @var $token Mage_OAuth_Model_Token */
        foreach ($models as $token) {
            $customerId = $token->getCustomerId();
            $id = $token->getId();
            $this->getRequest()->setParam('id', $id);
            Mage::unregister('application_params');
            $this->dispatch('oauth/myApplication/delete');
            $this->assertRedirectMatch($redirectUrl);
            $token = new Mage_OAuth_Model_Token;
            $token->load($id);
            $this->assertEquals(
                $token->getId(),
                $customerId ? null : $id,
                $customerId ? $message : $messageNonCustomer);
        }
    }
}
