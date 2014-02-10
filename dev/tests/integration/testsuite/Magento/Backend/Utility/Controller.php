<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Utility;

/**
 * A parent class for backend controllers - contains directives for admin user creation and authentication
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
class Controller extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOffSecretKey();

        $this->_auth = $this->_objectManager->get('Magento\Backend\Model\Auth');
        $this->_session = $this->_auth->getAuthStorage();
        $credentials = $this->_getAdminCredentials();
        $this->_auth->login($credentials['user'], $credentials['password']);
    }

    /**
     * Get credentials to login admin user
     *
     * @return array
     */
    protected function _getAdminCredentials()
    {
        return array(
            'user' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
        );
    }

    protected function tearDown()
    {
        $this->_auth->logout();
        $this->_auth = null;
        $this->_session = null;
        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * Utilize backend session model by default
     *
     * @param \PHPUnit_Framework_Constraint $constraint
     * @param string|null $messageType
     * @param string $messageManager
     */
    public function assertSessionMessages(
        \PHPUnit_Framework_Constraint $constraint, $messageType = null, $messageManager = 'Magento\Message\Manager'
    ) {
        parent::assertSessionMessages($constraint, $messageType, $messageManager);
    }
}
