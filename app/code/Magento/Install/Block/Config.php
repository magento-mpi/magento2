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
 * Config installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Install_Block_Config extends Magento_Install_Block_Abstract
{
    /**
     * @var string
     */
    protected $_template = 'config.phtml';

    /**
     * Install installer config
     *
     * @var Magento_Install_Model_Installer_Config
     */
    protected $_installerConfig = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Install_Model_Installer $installer
     * @param Magento_Install_Model_Wizard $installWizard
     * @param Magento_Core_Model_Session_Generic $session
     * @param Magento_Install_Model_Installer_Config $installerConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Install_Model_Installer $installer,
        Magento_Install_Model_Wizard $installWizard,
        Magento_Core_Model_Session_Generic $session,
        Magento_Install_Model_Installer_Config $installerConfig,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $installer, $installWizard, $session, $data);
        $this->_installerConfig = $installerConfig;
    }

    /**
     * Retrieve form data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/configPost');
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

    /**
     * @return bool
     */
    public function getSkipUrlValidation()
    {
        return $this->_session->getSkipUrlValidation();
    }

    /**
     * @return bool
     */
    public function getSkipBaseUrlValidation()
    {
        return $this->_session->getSkipBaseUrlValidation();
    }

    /**
     * @return array
     */
    public function getSessionSaveOptions()
    {
        return array(
            'files' => __('File System'),
            'db'    => __('Database'),
        );
    }

    /**
     * @return string
     */
    public function getSessionSaveSelect()
    {
        $html = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setName('config[session_save]')
            ->setId('session_save')
            ->setTitle(__('Save Session Files In'))
            ->setClass('required-entry')
            ->setOptions($this->getSessionSaveOptions())
            ->getHtml();
        return $html;
    }
}
