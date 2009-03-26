<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Staging_Model_Staging_State_Website_Create extends Enterprise_Staging_Model_Staging_State_Website_Abstract
{
    /**
     * Boolean flag that confirm to create event history record
     * after current state is done
     *
     * @var boolean
     */
    protected $_addToEventHistory = true;

    /**
     * Main run method of current state
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _run(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_createStaging($staging);
        return $this;
    }

    /**
     * Copy data of selected(mapped) items into staging websites and store views
     * (map information must be exists in staging mapper instance)
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_State_Website_Create
     */
    protected function _createStaging(Enterprise_Staging_Model_Staging $staging)
    {
        $stagingWebsites = $staging->getWebsitesCollection();
        foreach ($stagingWebsites as $stagingWebsite) {
            $this->_createWebsiteData($staging, $stagingWebsite);
            $this->_createStoresData($staging, $stagingWebsite);
        }
        return $this;
    }

    /**
     * Copy data of selected(mapped) items into staging website
     * (map information must be exists in staging mapper instance)
     *
     * @param Enterprise_Staging_Model_Staging          $staging
     * @param Enterprise_Staging_Model_Staging_Website  $stagingWebsite
     * @return unknown
     */
    protected function _createWebsiteData(Enterprise_Staging_Model_Staging $staging, Enterprise_Staging_Model_Staging_Website $stagingWebsite)
    {
        $usedItems = $this->getStaging()->getMapperInstance()
            ->getWebsiteUsedCreateItems($stagingWebsite->getMasterWebsiteId());
        foreach ($usedItems as $usedItem) {
            $itemXmlConfig = Enterprise_Staging_Model_Staging_Config::getStagingItem($usedItem['code']);
            if ($itemXmlConfig) {
                $adapter = $this->getItemAdapterInstanse($itemXmlConfig);
                $adapter->createItem($staging, $stagingWebsite, $itemXmlConfig);
            }
        }
        return $this;
    }

    /**
     * Copy data of selected(mapped) items into staging store view
     * (map information must be exists in staging mapper instance)
     *
     * @param Enterprise_Staging_Model_Staging          $staging
     * @param Enterprise_Staging_Model_Staging_Website  $stagingWebsite
     * @return unknown
     */
    protected function _createStoresData(Enterprise_Staging_Model_Staging $staging, Enterprise_Staging_Model_Staging_Website $stagingWebsite)
    {
        $stagingStores  = $stagingWebsite->getStoresCollection();
        foreach ($stagingStores as $stagingStore) {
            $usedItems = $this->getStaging()->getMapperInstance()
                ->getWebsiteUsedCreateItems($stagingWebsite->getMasterWebsiteId());
            foreach ($usedItems as $usedItem) {
                $itemXmlConfig = Enterprise_Staging_Model_Staging_Config::getStagingItem($usedItem['code']);
                if ($itemXmlConfig) {
                    $adapter = $this->getItemAdapterInstanse($itemXmlConfig);
                    if ($adapter) {
                        $adapter->createItem($staging, $stagingStore, $itemXmlConfig);
                    }
                }
            }
        }
        return $this;
    }
}