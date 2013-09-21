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

/**
 * A parent class for backend controllers - contains directives for admin user creation and authentication
 */
namespace Magento\Backend\Utility;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Controller extends \Magento\TestFramework\TestCase\ControllerAbstract
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
        var_dump("setUp");
        parent::setUp();

        \Mage::getSingleton('Magento\Backend\Model\Url')->turnOffSecretKey();

        var_dump("before");
        $this->_auth = \Mage::getSingleton('Magento\Backend\Model\Auth');
        var_dump("after");
        $this->_session = $this->_auth->getAuthStorage();
        var_dump("got session");
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
    }

    protected function tearDown()
    {
        $this->_auth->logout();
        $this->_auth = null;
        $this->_session = null;
        \Mage::getSingleton('Magento\Backend\Model\Url')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * Utilize backend session model by default
     *
     * @param \PHPUnit_Framework_Constraint $constraint
     * @param string|null $messageType
     * @param string $sessionModel
     */
    public function assertSessionMessages(
        \PHPUnit_Framework_Constraint $constraint, $messageType = null, $sessionModel = 'Magento\Backend\Model\Session'
    ) {
        parent::assertSessionMessages($constraint, $messageType, $sessionModel);
    }
}
