<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Staging_State_Website_Create extends Enterprise_Staging_Model_Staging_State_Website_Abstract
{
    /**
     * Main run method of current state
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     *
     * @return  Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _run(Enterprise_Staging_Model_Staging $staging)
    {
        $mapper = $staging->getMapperInstance();

        $this->_createStagingWebsite($staging);

        $this->_createStagingStoreGroup($staging);

        $this->_createStagingStoreView($staging);

        $stagingItems = $mapper->getStagingItems();

        $this->_createStagingItems($staging);

        foreach ($stagingItems as $stagingItem) {
            $adapter = $this->getItemAdapterInstanse($stagingItem);
            $adapter->create($staging);
            if (!empty($stagingItem->extends) && is_object($stagingItem->extends)) {
                foreach ($stagingItem->extends->children() AS $extendItem) {
                    if (!Enterprise_Staging_Model_Staging_Config::isItemModuleActive($extendItem)) {
                         continue;
                    }
                    $adapter = $this->getItemAdapterInstanse($extendItem);
                    $adapter->create($staging);
                }
            }
        }

        return $this;
    }

    /**
     * Set complete status into current staging
     *
     * @param Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _afterRun(Enterprise_Staging_Model_Staging $staging)
    {
        $staging->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE);

        return parent::_afterRun($staging);
    }

    /**
     * Create new staging website
     * (map information must be exists in staging mapper instance)
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     *
     * @return  Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _createStagingWebsite($staging)
    {
        Mage::getSingleton('enterprise_staging/staging_adapter_website')
            ->setEventStateCode($this->getEventStateCode())
            ->create($staging);

        return $this;
    }

    /**
     * Create new staging store group
     * (map information must be exists in staging mapper instance)
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     *
     * @return  Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _createStagingStoreGroup($staging)
    {
        Mage::getSingleton('enterprise_staging/staging_adapter_group')
            ->setEventStateCode($this->getEventStateCode())
            ->create($staging);

        return $this;
    }

    /**
     * Create new staging store views
     * (map information must be exists in staging mapper instance)
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     *
     * @return  Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _createStagingStoreView($staging)
    {
        Mage::getSingleton('enterprise_staging/staging_adapter_store')
            ->setEventStateCode($this->getEventStateCode())
            ->create($staging);

        return $this;
    }

    /**
     * Create staging items
     * (map information must be exists in staging mapper instance)
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     *
     * @return  Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _createStagingItems($staging)
    {
        Mage::getSingleton('enterprise_staging/staging_adapter_item')
            ->setEventStateCode($this->getEventStateCode())
            ->create($staging);

        return $this;
    }
}
