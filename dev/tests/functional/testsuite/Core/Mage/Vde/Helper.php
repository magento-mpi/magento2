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
class Core_Mage_Vde_Helper extends Mage_Selenium_AbstractHelper
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
        $baseUrl = $this->getConfigHelper()->getBaseUrl();
        $baseUrl = $baseUrl . $urlPrefix;
        $result = strpos($url, $baseUrl) !== false;
        return $result;
    }

    /**
     * Select specific Page Type
     *
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
        sleep(1);
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
        sleep(1);
    }

    /**
     * Are highlight blocks shown in iframe
     */
    public function areHighlightBlocksShown()
    {
        $this->assertEquals('vde_design', $this->getCurrentPage());

        $classStyle = array(
            ' and @style="display: block;"' => true,
            ' and @style="display: none;"' => false,
            '' => true
        );
        foreach ($classStyle as $classParam => $areShown){
            $this->addParameter('displayStyle', $classParam);
            if ($this->controlIsPresent('pageelement', 'highlight_containers')) {
                return $areShown;
            }
        }
    }

    /**
     * Open theme demo by theme id. By default opens first theme is the list
     *
     * @param int|null $id
     */
    public function openThemeDemo($id = null)
    {
        $themeContainerXpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
        $demoButtonXpath = $this->_getControlXpath('button', 'preview_demo_button');
        if ($id) {
            $themeContainerXpath .= "[@id='theme-id-" . $id . "']";
            $demoButtonXpath = "//li[@id='theme-id-" . $id . "']" . $demoButtonXpath;
        } else {
            $themeContainerXpath .= '[1]';
        }
        $this->waitForElement($themeContainerXpath);
        $this->getElement($themeContainerXpath)->click();
        $this->getElement($demoButtonXpath)->click();
        $this->waitForPageToLoad();
        if (!$id) {
            $id = $this->defineIdFromUrl();
        }
        $this->addParameter('themeId', $id);
        $this->validatePage();
    }
}
