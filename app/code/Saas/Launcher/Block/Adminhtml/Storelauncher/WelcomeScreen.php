<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Getting Started Tour Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_WelcomeScreen extends Mage_Backend_Block_Template
{
    /**
     * Flag stores the state whether Welcome Tour has been shown for customer or not
     *
     * @var Saas_Launcher_Model_Storelauncher_Flag
     */
    protected $_launcherFlag;

    /**
     * Admin user
     *
     * @var Mage_User_Model_User
     */
    protected $_adminUser;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_Storelauncher_Flag $flag
     * @param Mage_Backend_Model_Auth_Session $session
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_Storelauncher_Flag $flag,
        Mage_Backend_Model_Auth_Session $session,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_launcherFlag = $flag;
        $this->_launcherFlag->loadSelf();
        $this->_adminUser = $session->getUser();
    }

    /**
     * Check whether Welcome Screen has been already shown
     *
     * @return boolean
     */
    public function isWelcomeScreenShown()
    {
        return (bool)$this->_launcherFlag->getState();
    }

    /**
     * Get Admin user Name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->_adminUser->getFirstname();
    }
}
