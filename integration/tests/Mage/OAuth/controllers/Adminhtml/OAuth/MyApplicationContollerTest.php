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
        $this->loginToAdmin();
        $models = require realpath(dirname(__FILE__) . '/../../..')
                            . '/Model/_fixtures/tokenConsumerCreate.php';

        /** @var $token1 Mage_OAuth_Model_Token */
        $token1 = $models['token'][0];
        /** @var $token2 Mage_OAuth_Model_Token */
        $token2 = $models['token'][1];
        /** @var $token3 Mage_OAuth_Model_Token */
        $token3 = $models['token'][2];

        $tokenIds = array($token1->getId(), $token2->getId(), $token3->getId());
        $this->getRequest()->setParam('items', $tokenIds);
        $this->getRequest()->setParam('status', 0);
        $this->dispatch('admin/oAuth_myApplication/revoke');

        $token1->load($token1->getId());
        $token2->load($token2->getId());

        $message = 'Token has wrong is_revoked value.';
        $this->assertEquals(1, $token1->getIsRevoked(), 'Token is updated but it must be not.');
        $this->assertEquals(0, $token2->getIsRevoked(), $message);

        Mage::unregister('application_params');
        $this->getRequest()->setParam('status', 1);
        $this->dispatch('admin/oAuth_myApplication/revoke');

        $token2->load($token2->getId());
        $token3->load($token3->getId());

        $this->assertEquals(1, $token2->getIsRevoked(), $message);
        $this->assertEquals(1, $token3->getIsRevoked(), $message);
    }
}
