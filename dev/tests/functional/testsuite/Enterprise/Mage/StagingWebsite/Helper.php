<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StagingWebsite
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingWebsite_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * <p>Creates staging for website</p>
     *
     * @param string|array $websiteData
     * @param string $filename
     */
    public function createStagingWebsite($websiteData, $filename = 'StagingWebsite')
    {
        if (is_string($websiteData)) {
            $websiteData = $this->loadDataSet($filename, $websiteData);
        }
        $websiteData = $this->clearDataArray($websiteData);
        $settings = (isset($websiteData['settings']))
            ? $websiteData['settings']
            : array();
        $generalInfo = (isset($websiteData['general_information']))
            ? $websiteData['general_information']
            : array();
        $storeViews = (isset($websiteData['store_views']))
            ? $websiteData['store_views']
            : array();

        $this->clickButton('add_staging_website');
        $this->fillSettings($settings);
        if ($generalInfo) {
            $this->fillTab($generalInfo, 'general_information');
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
            $this->fillFieldset($settings, 'staging_website');
            $websiteId = $this->getControlAttribute('dropdown', 'source_website', 'selectedValue');
            $this->addParameter('id', $websiteId);
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
                $this->fillCheckbox('store_view', $action);
            }
        }
    }

    /**
     * <p>Open staging website<p>
     *
     * @param array $searchWebsiteData
     */
    public function openStagingWebsite(array $searchWebsiteData)
    {
        if ($searchWebsiteData) {
            if (isset($searchWebsiteData['filter_website_name'])) {
                $this->addParameter('elementTitle', $searchWebsiteData['filter_website_name']);
            }
            $this->searchAndOpen($searchWebsiteData, 'staging_websites_grid');
        }
    }

    /**
     * <p>Merge Website</p>
     *
     * @param string | array $mergeWebsiteData
     * @param string $filename
     */
    public function mergeWebsite($mergeWebsiteData, $filename = 'StagingWebsite')
    {
        if (is_string($mergeWebsiteData)) {
            $mergeWebsiteData = $this->loadDataSet($filename, $mergeWebsiteData);
        }
        $mergeWebsiteData = $this->clearDataArray($mergeWebsiteData);
        $searchWebsiteData = (isset($mergeWebsiteData['search_website']))
            ? $mergeWebsiteData['search_website']
            : array();
        $generalInfo = (isset($mergeWebsiteData['general_information']))
            ? $mergeWebsiteData['general_information']
            : array();
        $mergeConfig = (isset($mergeWebsiteData['merge_configuration']))
            ? $mergeWebsiteData['merge_configuration']
            : array();
        $scheduleMerge = (isset($mergeWebsiteData['schedule_merge']))
            ? $mergeWebsiteData['schedule_merge']
            : array();

        $this->openStagingWebsite($searchWebsiteData);
        if ($generalInfo) {
            $this->fillTab($generalInfo, 'general_information');
        }
        $this->clickButton('merge');
        $this->_fillMergeConfig($mergeConfig);
        if ($scheduleMerge) {
            $this->fillFieldset($scheduleMerge, 'schedule');
            $this->saveForm('schedule_merge');
        } else {
            $this->saveForm('merge_now');
        }

    }

    /**
     * Fills merge config
     *
     * @param $mergeConfig
     */
    protected function _fillMergeConfig($mergeConfig)
    {
        if ($mergeConfig) {
            $this->fillForm($mergeConfig);
            if (isset($mergeConfig['merge_to'])) {
                $this->addParameter('dropdownXpath', $this->_getControlXpath('dropdown', 'merge_to'));
                $this->addParameter('optionText', $mergeConfig['merge_to']);
                $websiteToId = $this->getControlAttribute('pageelement', 'dropdown_option_text', 'value');
                $this->addParameter('websiteToId', $websiteToId);
                if (isset($mergeConfig['define_stores'])) {
                    $websiteFromId = $this->getControlAttribute('pageelement', 'merge_from', 'value');;
                    $this->addParameter('websiteFromId', $websiteFromId);
                    $storeIndex = 3;
                    foreach ($mergeConfig['define_stores'] as $storeViews) {
                        $this->addParameter('ind', $storeIndex++);
                        $this->clickButton('add_new_store_view_map', false);
                        $this->waitForAjax();
                        $this->fillFieldset($storeViews, 'merge_configuration');
                        if ($this->alertIsPresent()) {
                            $this->fail($this->alertText());
                        }
                    }
                }
            }
        }
    }

    /**
     * <p>Build Frontend URL for staging website</p>
     *
     * @param string $stagingWebsiteCode
     *
     * @return string $frontendUrl
     */
    public function buildFrontendUrl($stagingWebsiteCode)
    {
        $oldFrontendUrl = $this->getConfigHelper()->getAreaBaseUrl('frontend');
        if (preg_match('/index.php/', $oldFrontendUrl)) {
            $nodes = explode('index.php', $oldFrontendUrl);
            $frontendUrl = $nodes[0] . 'staging/' . $stagingWebsiteCode . '/index.php/';
        } else {
            $frontendUrl = $oldFrontendUrl . 'staging/' . $stagingWebsiteCode . '/';
        }
        return $frontendUrl;
    }
}
