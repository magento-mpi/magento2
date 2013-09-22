<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for "Reward Points Lifetime"
 *
 */
namespace Magento\Reward\Model\System\Config\Backend;

class Expiration extends \Magento\Core\Model\Config\Value
{
    const XML_PATH_EXPIRATION_DAYS = 'magento_reward/general/expiration_days';

    /**
     * Update history expiration date to simplify frontend calculations
     *
     * @return \Magento\Reward\Model\System\Config\Backend\Expiration
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        $websiteIds = array();
        if ($this->getWebsiteCode()) {
            $websiteIds = array(\Mage::app()->getWebsite($this->getWebsiteCode())->getId());
        } else {
            $collection = \Mage::getResourceModel('Magento\Core\Model\Resource\Config\Data\Collection')
                ->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS)
                ->addFieldToFilter('scope', 'websites');
            $websiteScopeIds = array();
            foreach ($collection as $item) {
                $websiteScopeIds[] = $item->getScopeId();
            }
            foreach (\Mage::app()->getWebsites() as $website) {
                /* @var $website \Magento\Core\Model\Website */
                if (!in_array($website->getId(), $websiteScopeIds)) {
                    $websiteIds[] = $website->getId();
                }
            }
        }
        if (count($websiteIds) > 0) {
            \Mage::getResourceModel('Magento\Reward\Model\Resource\Reward\History')
                ->updateExpirationDate($this->getValue(), $websiteIds);
        }

        return $this;
    }

    /**
     * The same as _beforeSave, but executed when website config extends default values
     *
     * @return \Magento\Reward\Model\System\Config\Backend\Expiration
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        if ($this->getWebsiteCode()) {
            $default = (string)$this->_coreConfig->getValue(self::XML_PATH_EXPIRATION_DAYS, 'default');
            $websiteIds = array(\Mage::app()->getWebsite($this->getWebsiteCode())->getId());
            \Mage::getResourceModel('Magento\Reward\Model\Resource\Reward\History')
                ->updateExpirationDate($default, $websiteIds);
        }
        return $this;
    }
}
