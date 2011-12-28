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
 * Test model admin My Applications controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_OAuth_Adminhtml_OAuth_MyApplicationControllerTest extends Magento_Test_ControllerTestCaseAbstract
{
    /**
     * Test update revoke status
     */
    public function testRevokeStatusUpdate()
    {

        //generate test item
        $models = require realpath(dirname(__FILE__) . '/../../..')
                . '/Model/_fixtures/tokenConsumerCreate.php';

        /** @var $token2 Mage_OAuth_Model_Token */
        $token1 = $models['token']['customer'][0]; //token which not related to admin, revoke = 0
        /** @var $token1 Mage_OAuth_Model_Token */
        $token2 = $models['token']['customer'][1]; //token which not related to admin, revoke = 1
        /** @var $token3 Mage_OAuth_Model_Token */
        $token3 = $models['token']['admin'][0]; //revoke = 0
        /** @var $token4 Mage_OAuth_Model_Token */
        $token4 = $models['token']['admin'][1]; //revoke = 1

        $revoked = 0;
        $tokenIds = array(
            $token1->getId(),
            $token2->getId(),
            $token3->getId(),
            $token4->getId());
        $this->getRequest()->setParam('items', $tokenIds);
        $this->getRequest()->setParam('status', $revoked);

        $this->loginToAdmin();
        $this->dispatch('admin/oAuth_myApplication/revoke');

        $token2->load($token2->getId());
        $token3->load($token3->getId());
        $token4->load($token4->getId());

        $message = 'Token has wrong revoked value.';
        $this->assertEquals(1, $token2->getRevoked(), 'Token is updated but it must be not.');
        $this->assertEquals($revoked, $token3->getRevoked(), $message);
        $this->assertEquals($revoked, $token4->getRevoked(), $message);

        $revoked = 1;
        Mage::unregister('application_params');
        $this->getRequest()->setParam('status', $revoked);
        $this->dispatch('admin/oAuth_myApplication/revoke');

        $token1->load($token1->getId());
        $token3->load($token3->getId());
        $token4->load($token4->getId());

        $this->assertEquals(0, $token1->getRevoked(), 'Token is updated but it must be not.');
        $this->assertEquals($revoked, $token3->getRevoked(), $message);
        $this->assertEquals($revoked, $token4->getRevoked(), $message);
    }
}
