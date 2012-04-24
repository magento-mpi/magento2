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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingWebsite_Helper extends Mage_Selenium_TestCase
{
    /**
     * <p>Creates staging for website</p>
     *
     * @param string|array $websiteData
     */
    public function createStagingWebsite($websiteData)
    {
        if (is_string($websiteData)) {
            $websiteData = $this->loadData($websiteData);
        }
        $websiteData = $this->arrayEmptyClear($websiteData);
        $settings = (isset($websiteData['settings'])) ? $websiteData['settings'] : array();
        $generalInfo = (isset($websiteData['general_information'])) ? $websiteData['general_information'] : array();
        $storeViews = (isset($websiteData['store_views'])) ? $websiteData['store_views'] : array();

        $this->clickButton('add_staging_website');
        $this->fillSettings($settings);
        if ($generalInfo) {
            $this->fillForm($generalInfo, 'general_information');
        }
        $this->selectStoreViews($storeViews);
        $this->saveForm('create');
    }

    /**
     * <p>Fills Settings tab</p>
     *
     * @param array $settings
     */
    public function fillSettings(array $settings)
    {
        if ($settings) {
            $xpath = $this->_getControlXpath('dropdown', 'source_website');
            $websiteId = $this->getValue($xpath . '/option[text()="' . $settings['source_website'] . '"]');
            $this->addParameter('id', $websiteId);
            $this->fillForm($settings);
        }
        $this->clickButton('continue');
    }

    /**
     * <p>Select store views for staging website</p>
     *
     * @param array $storeViews
     */
    public function selectStoreViews(array $storeViews)
    {
        if ($storeViews) {
            foreach ($storeViews as $storeViewName => $action) {
                $this->addParameter('storeView', $storeViewName);
                $this->fillForm(array('store_view' => $action));
            }
        }
    }

    /**
     * <p>Open staging website<p>
     *
     * @param array $searchWebsiteData
     * @return bool
     */
    public function openStagingWebsite(array $searchWebsiteData)
    {
        if ($searchWebsiteData) {
            if (isset($searchWebsiteData['filter_website_name'])) {
                $this->addParameter('elementTitle', $searchWebsiteData['filter_website_name']);
            }
            return $this->searchAndOpen($searchWebsiteData);
        }
    }

    /**
     * <p>Merge Website</p>
     *
     * @param string | array $mergeWebsiteData
     */
    public function mergeWebsite($mergeWebsiteData)
    {
        if (is_string($mergeWebsiteData)) {
            $mergeWebsiteData = $this->loadData($mergeWebsiteData);
        }
        $mergeWebsiteData = $this->arrayEmptyClear($mergeWebsiteData);
        $searchWebsiteData = (isset($mergeWebsiteData['search_website']))
                ? $mergeWebsiteData['search_website'] : array();
        $generalInfo = (isset($mergeWebsiteData['general_information']))
                ? $mergeWebsiteData['general_information'] : array();
        $mergeConfig = (isset($mergeWebsiteData['merge_configuration']))
                ? $mergeWebsiteData['merge_configuration'] : array();
        $scheduleMerge = (isset($mergeWebsiteData['schedule_merge']))
                ? $mergeWebsiteData['schedule_merge'] : array();

        $this->openStagingWebsite($searchWebsiteData);
        if ($generalInfo) {
            $this->fillForm($generalInfo);
        }
        $this->clickButton('merge');
        if ($mergeConfig) {
            $this->fillForm($mergeConfig);
            if (isset($mergeConfig['merge_to'])) {
                $xpathTo = $this->_getControlXpath('dropdown', 'merge_to');
                $websiteToId = $this->getValue($xpathTo . '/option[text()="' . $mergeConfig['merge_to'] . '"]');
                $this->addParameter('websiteToId', $websiteToId);
                if (isset($mergeConfig['define_stores'])) {
                    $xpathFrom = $this->_getControlXpath('pageelement', 'merge_from');
                    $websiteFromId = $this->getValue($xpathFrom);
                    $this->addParameter('websiteFromId', $websiteFromId);
                    $i = 3;
                    foreach ($mergeConfig['define_stores'] as $storeViews) {
                        $this->addParameter('ind', $i++);
                        $this->clickButton('add_new_store_view_map', false);
                        $this->waitForAjax();
                        $this->fillForm($storeViews);
                        if ($this->isAlertPresent()) {
                            $this->fail($this->getAlert());
                        }
                    }
                }
            }
        }
        if ($scheduleMerge) {
            $this->fillForm($scheduleMerge);
            $this->saveForm('schedule_merge');
        } else {
            $this->saveForm('merge_now');
        }

    }

    /**
     * <p>Build Frontend URL for staging website</p>
     *
     * @param string $stagingWebsiteCode
     * @return string $frontendUrl
     */
    public function buildFrontendUrl($stagingWebsiteCode)
    {
        $oldFrontendUrl = $this->_configHelper->getAreaBaseUrl('frontend');
        if (preg_match('/index.php/', $oldFrontendUrl)) {
            $nodes = explode('index.php', $oldFrontendUrl);
            $frontendUrl = $nodes[0] . 'staging/' . $stagingWebsiteCode . '/index.php/';
        } else {
            $frontendUrl = $oldFrontendUrl . 'staging/' . $stagingWebsiteCode . '/';
        }
        return $frontendUrl;
    }
}
