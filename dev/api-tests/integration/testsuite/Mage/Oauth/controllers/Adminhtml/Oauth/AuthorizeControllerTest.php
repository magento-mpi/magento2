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
 * Test model admin My Applications controller
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Api Team <api-team@magento.com>
 *
 */
class Mage_Oauth_Adminhtml_Oauth_AuthorizeControllerTest extends Magento_Test_TestCase_ControllerAbstract
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
        list($this->_token, $this->_consumer) = require TEST_FIXTURE_DIR . 'Oauth/Token/token.php';

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
