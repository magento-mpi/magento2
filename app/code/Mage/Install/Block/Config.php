<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Config extends Mage_Install_Block_Abstract
{
    protected $_template = 'config.phtml';

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
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = Mage::getSingleton('Mage_Install_Model_Session')->getConfigData(true);
            if (empty($data)) {
                $data = Mage::getModel('Mage_Install_Model_Installer_Config')->getFormData();
            }
            else {
                $data = new Varien_Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }

    public function getSkipUrlValidation()
    {
        return Mage::getSingleton('Mage_Install_Model_Session')->getSkipUrlValidation();
    }

    public function getSkipBaseUrlValidation()
    {
        return Mage::getSingleton('Mage_Install_Model_Session')->getSkipBaseUrlValidation();
    }

    public function getSessionSaveOptions()
    {
        return array(
            'files' => __('File System'),
            'db'    => __('Database'),
        );
    }

    public function getSessionSaveSelect()
    {
        $html = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName('config[session_save]')
            ->setId('session_save')
            ->setTitle(__('Save Session Files In'))
            ->setClass('required-entry')
            ->setOptions($this->getSessionSaveOptions())
            ->getHtml();
        return $html;
    }
}
