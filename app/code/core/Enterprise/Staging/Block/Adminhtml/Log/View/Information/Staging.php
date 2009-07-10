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

/**
 * Staging History Item View
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log_View_Information_Staging extends Enterprise_Staging_Block_Adminhtml_Log_View_Information_Default
{
    /**
     * Retrieves website model which was used as source
     *
     * @return Mage_Core_Model_Website
     */
    public function getSourceWebsite()
    {
        $mapData = $this->_mapper->getMergeMapData();

        if (isset($mapData['websites'])) {
            //array_pop used bc in previous versions of staging there was bug
            //and trash info was saved in map before correct website_id
            $websiteId = array_pop($mapData['websites']['from']);
            return Mage::app()->getWebsite($websiteId);
        }

        /**
         * If did not have data in merge map then using staging_website_id or
         * master_website_id columns
         */
        if ($this->getLog()->getCode() == 'create') {
            return $this->getLog()->getStaging()->getMasterWebsite();
        } else {
            return $this->getLog()->getStaging()->getStagingWebsite();
        }
    }

    /**
     * Retrieves website model which was used as target
     *
     * @return Mage_Core_Model_Website
     */
    public function getTargetWebsite()
    {
        $mapData = $this->_mapper->getMergeMapData();

        if (isset($mapData['websites'])) {
            //array_pop used bc in previous versions of staging there was bug
            //and trash info was saved in map before correct website_id
            $websiteId = array_pop($mapData['websites']['to']);
            return Mage::app()->getWebsite($websiteId);
        }

        /**
         * If did not have data in merge map then using staging_website_id or
         * master_website_id columns
         */
        if ($this->getLog()->getCode() == 'create') {
            return $this->getLog()->getStaging()->getStagingWebsite();
        } else {
            return $this->getLog()->getStaging()->getMasterWebsite();
        }
    }

    /**
     * Prepares store view mapping which was used in merge
     * in case store view mapping was not defined returns message
     *
     * @return mixed
     */
    public function getStoreViewsMap()
    {
        $mapData = $this->_mapper->getMergeMapData();

        $map = array();

        if (isset($mapData['stores'])) {
            foreach ($mapData['stores'] as $store) {
                foreach ($store['from'] as $key => $storeViewId) {
                    $_fromStoreView = Mage::app()->getStore($storeViewId);
                    $_toStoreView = Mage::app()->getStore($store['to'][$key]);
                    if ($_fromStoreView && $_toStoreView) {
                        $map[] = array(
                            'from'  => $_fromStoreView,
                            'to'    => $_toStoreView
                        );
                    }
                }
            }
        }

        if (!count($map)) {
            return Mage::helper('enterprise_staging')->__('There was no mapping defined for store views.');
        }

        return $map;
    }

    /**
     * Generate link for last scheduled merge log entry
     * if not available return information message
     *
     * @return string
     */
    public function getScheduleMergeLink()
    {
        $logId = $this->getLog()->getLastScheduleMergeLogId();
        if ($logId) {
            $url = $this->getUrl('*/*/view', array('id' => $this->getLog()->getLastScheduleMergeLogId()));
            return '<a href="' .  $url. '">' . $url . '</a>';
        }

        return Mage::helper('enterprise_staging')->__('No information available');
    }
}
