<?php
/**
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Tenant_Command_UnlockAdmin
{
    /**
     * @var Enterprise_Pci_Model_Resource_Admin_User|null
     */
    protected $_resourceModel = null;

    /**
     * @param Enterprise_Pci_Model_Resource_Admin_User $resourceModel
     */
    public function __construct(
        Enterprise_Pci_Model_Resource_Admin_User $resourceModel
    ) {
        $this->_resourceModel = $resourceModel;
    }

    /**
     * Execute command unlockAdmin
     *
     * @param array $params
     * @return void
     */
    public function execute(array $params = array())
    {
        $this->_resourceModel->unlock($params['adminId']);
    }
}
