<?php
/**
 * Command model for processing ResetAdminPassword request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Tenant_Command_ResetAdminPassword
{
    /**
     * @var Mage_User_Model_User|null
     */
    protected $_userModel = null;

    /**
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendHelper = null;

    /**
     * @param Mage_User_Model_User $userModel
     * @param Magento_Backend_Helper_Data $helper
     */
    public function __construct(
        Mage_User_Model_User $userModel,
        Magento_Backend_Helper_Data $helper
    ) {
        $this->_userModel = $userModel;
        $this->_backendHelper = $helper;
    }

    /**
     * Execute command resetAdminPassword
     *
     * @param array $params
     * @return void
     */
    public function execute(array $params = array())
    {
        $user = $this->_userModel->load($params['adminId']);

        if ($user->getId()) {

            $user->changeResetPasswordLinkToken($this->_backendHelper->generateResetPasswordLinkToken())
                ->save();

            $user->setEmail($params['adminEmail']);
            $user->sendPasswordResetConfirmationEmail();
        }
    }
}
