<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A parent class for backend controllers - contains directives for admin user creation and authentication
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
class Mage_Backend_Utility_Controller extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Model_Auth
     */
    protected $_auth;

    /**
     * Whether absence of admin error messages has to be asserted automatically upon a test completion
     *
     * @var bool
     */
    protected $_assertAdminErrors = true;

    protected function setUp()
    {
        parent::setUp();

        Mage::setCurrentArea('adminhtml');
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $this->_auth = Mage::getModel('Mage_Backend_Model_Auth');
        $this->_session = $this->_auth->getAuthStorage();
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    protected function tearDown()
    {
        $this->_assertAdminErrors = true;
        $this->_auth->logout();
        $this->_auth = null;
        $this->_session = null;

        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();

        parent::tearDown();
    }

    /**
     * Ensure that there were no error messages displayed on the admin panel
     */
    protected function assertPostConditions()
    {
        if (!$this->_request || !$this->_assertAdminErrors) {
            return;
        }
        // equalTo() is intentionally used instead of isEmpty() to provide the informative diff
        $this->assertAdminMessages($this->equalTo(array()), Mage_Core_Model_Message::ERROR);
    }

    /**
     * Assert that the actual messages appearing on the admin panel meet expectations
     *
     * @param PHPUnit_Framework_Constraint $constraint Constraint to compare admin messages against
     * @param string|null $messageType Message type filter, one of the constants Mage_Core_Model_Message::*
     */
    public function assertAdminMessages(PHPUnit_Framework_Constraint $constraint, $messageType = null)
    {
        $this->_assertAdminErrors = false;
        /** @var $session Mage_Backend_Model_Session */
        $session = $this->_objectManager->get('Mage_Backend_Model_Session');
        $actualMessages = array();
        /** @var $message Mage_Core_Model_Message_Abstract */
        foreach ($session->getMessages()->getItems($messageType) as $message) {
            $actualMessages[] = $message->getText();
        }
        $this->assertThat($actualMessages, $constraint, 'Admin panel messages do not meet expectations');
    }
}
