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
     * @return \Magento\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = Mage::getSingleton('Magento_Install_Model_Session')->getConfigData(true);
            if (empty($data)) {
                $data = Mage::getModel('Magento_Install_Model_Installer_Config')->getFormData();
            } else {
                $data = new \Magento\Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }
}
