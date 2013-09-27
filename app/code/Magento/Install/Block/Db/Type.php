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
 * Common database config installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Install_Block_Db_Type extends Magento_Core_Block_Template
{
    /**
     * Db title
     *
     * @var string
     */
    protected $_title;

    /**
     * Install installer config
     *
     * @var Magento_Install_Model_Installer_Config
     */
    protected $_installerConfig = null;

    /**
     * Install installer config
     *
     * @var Magento_Core_Model_Session_Generic
     */
    protected $_session;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Install_Model_Installer_Config $installerConfig
     * @param Magento_Core_Model_Session_Generic $session
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Install_Model_Installer_Config $installerConfig,
        Magento_Core_Model_Session_Generic $session,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_installerConfig = $installerConfig;
        $this->_session = $session;
    }

    /**
     * Return Db title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Retrieve configuration form data object
     *
     * @return Magento_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = $this->_session->getConfigData(true);
            if (empty($data)) {
                $data = $this->_installerConfig->getFormData();
            } else {
                $data = new Magento_Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }
}
