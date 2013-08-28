<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for "Reward Points Lifetime"
 *
 */
class Enterprise_Reward_Model_System_Config_Backend_Expiration extends Magento_Core_Model_Config_Data
{
    const XML_PATH_EXPIRATION_DAYS = 'enterprise_reward/general/expiration_days';

    /**
     * Update history expiration date to simplify frontend calculations
     *
     * @return Enterprise_Reward_Model_System_Config_Backend_Expiration
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        $websiteIds = array();
        if ($this->getWebsiteCode()) {
            $websiteIds = array(Mage::app()->getWebsite($this->getWebsiteCode())->getId());
        } else {
            $collection = Mage::getResourceModel('Magento_Core_Model_Resource_Config_Data_Collection')
                ->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS)
                ->addFieldToFilter('scope', 'websites');
            $websiteScopeIds = array();
            foreach ($collection as $item) {
                $websiteScopeIds[] = $item->getScopeId();
            }
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Magento_Core_Model_Website */
                if (!in_array($website->getId(), $websiteScopeIds)) {
                    $websiteIds[] = $website->getId();
                }
            }
        }
        if (count($websiteIds) > 0) {
            Mage::getResourceModel('Enterprise_Reward_Model_Resource_Reward_History')
                ->updateExpirationDate($this->getValue(), $websiteIds);
        }

        return $this;
    }

    /**
     * The same as _beforeSave, but executed when website config extends default values
     *
     * @return Enterprise_Reward_Model_System_Config_Backend_Expiration
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        if ($this->getWebsiteCode()) {
            $default = (string)Mage::getConfig()->getNode('default/' . self::XML_PATH_EXPIRATION_DAYS);
            $websiteIds = array(Mage::app()->getWebsite($this->getWebsiteCode())->getId());
            Mage::getResourceModel('Enterprise_Reward_Model_Resource_Reward_History')
                ->updateExpirationDate($default, $websiteIds);
        }
        return $this;
    }
}
