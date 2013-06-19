<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for saving certificate file in case of using certificate based authentication
 */
class Mage_Paypal_Model_System_Config_Backend_Cert extends Mage_Core_Model_Config_Data
{
    /**
     * Process additional data before save config
     *
     * @return Mage_Paypal_Model_System_Config_Backend_Cert
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            Mage::getModel('Mage_Paypal_Model_Cert')->loadByWebsite($this->getScopeId())->delete();
        }

        if (!isset($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])) {
            return $this;
        }
        $tmpPath = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
        if ($tmpPath && file_exists($tmpPath)) {
            if (!filesize($tmpPath)) {
                Mage::throwException(Mage::helper('Mage_Paypal_Helper_Data')->__('The PayPal certificate file is empty.'));
            }
            $this->setValue($_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']);
            $content = Mage::helper('Mage_Core_Helper_Data')->encrypt(file_get_contents($tmpPath));
            Mage::getModel('Mage_Paypal_Model_Cert')->loadByWebsite($this->getScopeId())
                ->setContent($content)
                ->save();
        }
        return $this;
    }

    /**
     * Process object after delete data
     *
     * @return Mage_Paypal_Model_System_Config_Backend_Cert
     */
    protected function _afterDelete()
    {
        Mage::getModel('Mage_Paypal_Model_Cert')->loadByWebsite($this->getScopeId())->delete();
        return $this;
    }
}
