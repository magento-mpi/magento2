<?php
/**
 * Config source model for available tenant domains
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_Config_Source_Domains
{
    /**
     * Main Magento config
     *
     * @var Saas_Backend_Model_Config_Backend_Domains
     */
    protected $_backendModel;

    /**
     * Create instance of current class with appropriate parameters
     *
     * @param Saas_Backend_Model_Config_Backend_Domains $backendModel
     */
    public function __construct(Saas_Backend_Model_Config_Backend_Domains $backendModel)
    {
        $this->_backendModel = $backendModel;
    }

    /**
     * Retrieve tenant domains list allowed to select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_backendModel->getAvailableDomains();
    }
}
