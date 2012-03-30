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
 * @package     Mage_Oauth
 * @author      Magento Api Team <api-team@magento.com>
 *
 */
class Mage_Oauth_Adminhtml_Oauth_AuthorizeControllerTest extends Magento_Test_ControllerTestCaseAbstract
{
    /**
     * Consumer fixture
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected $_consumer;

    /**
     * Token fixture
     *
     * @var Mage_Oauth_Model_Token
     */
    protected $_token;

    /**
     * Get consumer data
     */
    protected function _getFixture()
    {
        list($this->_token, $this->_consumer) = require dirname(__FILE__).'/../../_fixtures/token.php';

        $this->addModelToDelete($this->_consumer);
        $this->addModelToDelete($this->_token);
    }

    /**
     * Test confirm action
     */
    public function testConfirmAction()
    {
        //generate test items
        $this->_getFixture();

        $this->loginToAdmin();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['oauth_token'] = $this->_token->getToken();

        Mage::unregister('application_params');
        $dispatchPath = 'admin/oauth_authorize/confirm';
        $this->dispatch($dispatchPath);
        $this->assertRedirect();    //$this->_token->getCallbackUrl()
    }

    /**
     * Test confirm action if no callback specified
     */
    public function testOobConfirmAction()
    {
        //generate test items
        $this->_getFixture();

        $this->loginToAdmin();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['oauth_token'] = $this->_token->getToken();

        $this->_token->setCallbackUrl(Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED);
        $this->_token->save();

        Mage::unregister('application_params');
        $dispatchPath = 'admin/oauth_authorize/confirm';
        $this->dispatch($dispatchPath);
        $this->_token->load($this->_token->getId());

        $this->assertContains($this->_token->getVerifier(), $this->getResponse()->getBody());
    }

    /**
     * Test reject action
     */
    public function testRejectAction()
    {
        //generate test items
        $this->_getFixture();

        $this->loginToAdmin();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['oauth_token'] = $this->_token->getToken();

        Mage::unregister('application_params');
        $dispatchPath = 'admin/oauth_authorize/reject';
        $this->dispatch($dispatchPath);
        $this->assertRedirect();
    }
}
