<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_Vde_Helper extends Mage_Selenium_TestCase
{
    /**
     * Verify url Vde prefix
     *
     * @param string $url
     * @return bool
     */
    public function isVdeRouter($url)
    {
        $urlPrefix = $this->getUrlPrefix();
        $baseUrl = $this->_configHelper->getBaseUrl();
        $baseUrl = $baseUrl . $urlPrefix;
        $result = strpos($url, $baseUrl) !== false;
        return $result;
    }

    /**
     * @param string $pageType
     */
    public function selectPageHandle($pageType)
    {
        $this->clickControl('dropdown','page_selector', false);
        $this->addParameter('pageType', $pageType);
        $this->waitForElementVisible($this->_getControlXpath('field', 'page_type_selector'));
        $this->clickControl('field','page_type_selector', false);
        $this->waitForFrameToLoad('vde_container_frame');
    }

    /**
     * Check if highlight option is enabled
     *
     * @return bool
     */
    public function isHighlightEnabled()
    {
        $this->assertEquals('vde_design', $this->getCurrentPage());

        $highlightStates = array(
            '' => false,
            ' checked' => true
        );
        foreach ($highlightStates as $classParam => $isEnabled){
            $this->addParameter('isChecked', $classParam);
            if ($this->controlIsPresent('checkbox', 'highlight')) {
                return $isEnabled;
            }
        }
    }

    /**
     * Enable highlight option in VDE toolbar
     */
    public function enableHighlight()
    {
        if (!$this->isHighlightEnabled()) {
            $this->clickControl('dropdown', 'view_options', false);
            $this->clickControl('checkbox', 'highlight', false);
        }
    }

    /**
     * Disable highlight option in VDE toolbar
     */
    public function disableHighlight()
    {
        if ($this->isHighlightEnabled()) {
            $this->clickControl('dropdown', 'view_options', false);
            $this->clickControl('checkbox', 'highlight', false);
        }
    }
}
