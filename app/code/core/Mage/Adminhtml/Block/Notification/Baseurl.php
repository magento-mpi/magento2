<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Notification_Baseurl extends Mage_Adminhtml_Block_Template
{
    /**
     * Get url for config settings where base url option can be changed
     *
     * @return string | false
     */
    public function getConfigUrl()
    {
        $defaultUnsecure= (string) Mage::getConfig()->getNode('default/'.Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
        $defaultSecure  = (string) Mage::getConfig()->getNode('default/'.Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL);

        if ($defaultSecure == Mage_Core_Model_Store::BASE_URL_PLACEHOLDER
            || $defaultUnsecure == Mage_Core_Model_Store::BASE_URL_PLACEHOLDER
        ) {
            return $this->getUrl('adminhtml/system_config/edit', array('section'=>'web'));
        }

        $configData = Mage::getModel('Mage_Core_Model_Config_Data');
        $dataCollection = $configData->getCollection()
            ->addValueFilter(Mage_Core_Model_Store::BASE_URL_PLACEHOLDER);

        $url = false;
        foreach ($dataCollection as $data) {
            if ($data->getScope() == 'stores') {
                $code = Mage::app()->getStore($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', array('section'=>'web', 'store'=>$code));
            }
            if ($data->getScope() == 'websites') {
                $code = Mage::app()->getWebsite($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', array('section'=>'web', 'website'=>$code));
            }

            if ($url) {
                return $url;
            }
        }
        return $url;
    }
}
