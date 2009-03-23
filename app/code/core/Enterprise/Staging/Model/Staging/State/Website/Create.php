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
    protected $_addToEventHistory = true;

    protected function _run($staging = null)
    {
        if (is_null($staging)) {
            $staging = $this->getStaging();
        }
        $this->_createStaging($staging);
        return $this;
    }

    protected function _createStaging($staging = null)
    {
        if (is_null($staging)) {
            $staging = $this->getStaging();
        }

        $websites = $staging->getWebsitesCollection();
        foreach ($websites as $website) {
            $this->_createWebsiteData($website);
            $this->_createStoresData($website);
        }
        return $this;
    }

    protected function _createWebsiteData($website = null)
    {
        if (is_null($website)) {
            $website = $this->getWebsite();
        }

        $stagingItems   = Enterprise_Staging_Model_Staging_Config::getStagingItems();
        $usedItems      = $this->getStaging()->getMapperInstance()
            ->getWebsiteUsedCreateItems($website->getMasterWebsiteId());

        foreach ($usedItems as $usedItem) {
            $itemXmlConfig = $stagingItems->{$usedItem['code']};
            $adapter = $this->getItemAdapterInstanse($itemXmlConfig);
            if ($adapter) {
                $adapter->createItem($website, $itemXmlConfig);
            }
        }
        return $this;
    }

    protected function _createStoresData($website = null)
    {
        if (is_null($website)) {
            $website = $this->getWebsite();
        }

        $stagingItems   = Enterprise_Staging_Model_Staging_Config::getStagingItems();

        $stagingStores  = $website->getStoresCollection();

        foreach ($stagingStores as $stagingStore) {
            $usedItems = $this->getStaging()->getMapperInstance()
                ->getWebsiteUsedCreateItems($website->getMasterWebsiteId());
            foreach ($usedItems as $usedItem) {
                $itemXmlConfig = $stagingItems->{$usedItem['code']};
                $adapter = $this->getItemAdapterInstanse($itemXmlConfig);
                if ($adapter) {
                    $adapter->createItem($stagingStore, $itemXmlConfig);
                }
            }
        }
        return $this;
    }
}