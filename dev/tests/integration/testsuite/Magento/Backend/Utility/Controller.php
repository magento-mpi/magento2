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
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
class Magento_Backend_Utility_Controller extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * @var Magento_Backend_Model_Auth
     */
    protected $_auth;

    protected function setUp()
    {
        parent::setUp();

        Mage::getSingleton('Magento_Backend_Model_Url')->turnOffSecretKey();

        $this->_auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $this->_session = $this->_auth->getAuthStorage();
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    protected function tearDown()
    {
        $this->_auth->logout();
        $this->_auth = null;
        $this->_session = null;
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * Utilize backend session model by default
     *
     * @param PHPUnit_Framework_Constraint $constraint
     * @param string|null $messageType
     * @param string $sessionModel
     */
    public function assertSessionMessages(
        PHPUnit_Framework_Constraint $constraint, $messageType = null, $sessionModel = 'Magento_Backend_Model_Session'
    ) {
        parent::assertSessionMessages($constraint, $messageType, $sessionModel);
    }
}
