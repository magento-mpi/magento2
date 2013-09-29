<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Abstract installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Install_Block_Abstract extends Magento_Core_Block_Template
{
    /**
     * Installer model
     *
     * @var Magento_Install_Model_Installer
     */
    protected $_installer;

    /**
     * Wizard model
     *
     * @var Magento_Install_Model_Wizard
     */
    protected $_installWizard;

    /**
     * Install session
     *
     * @var Magento_Core_Model_Session_Generic
     */
    protected $_session;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Install_Model_Installer $installer
     * @param Magento_Install_Model_Wizard $installWizard
     * @param Magento_Core_Model_Session_Generic $session
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Install_Model_Installer $installer,
        Magento_Install_Model_Wizard $installWizard,
        Magento_Core_Model_Session_Generic $session,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_installer = $installer;
        $this->_installWizard = $installWizard;
        $this->_session = $session;
    }


    /**
     * Retrieve installer model
     *
     * @return Magento_Install_Model_Installer
     */
    public function getInstaller()
    {
        return $this->_installer;
    }
    
    /**
     * Retrieve wizard model
     *
     * @return Magento_Install_Model_Wizard
     */
    public function getWizard()
    {
        return $this->_installWizard;
    }
    
    /**
     * Retrieve current installation step
     *
     * @return Magento_Object
     */
    public function getCurrentStep()
    {
        return $this->getWizard()->getStepByRequest($this->getRequest());
    }
}
