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
     * @return Core_Mage_Vde_Helper
     */
    public function selectPageHandle($pageType)
    {
        $this->addParameter('pageType', $pageType);
        if (!$this->controlIsVisible('field', 'page_type_selector')) {
            $this->clickControl('dropdown','page_selector', false);
            $this->waitForControlVisible('field', 'page_type_selector');
        }
        $this->clickControl('field','page_type_selector', false);
        $this->waitForPageToLoad();

        return $this;
    }

    /**
     * Expand page handle
     *
     * @param string $pageType
     * @return Core_Mage_Vde_Helper
     */
    public function expandPageHandle($pageType)
    {
        $this->addParameter('pageType', $pageType);
        $xpath = $this->_getControlXpath('field', 'page_type_selector');
        if (!$this->controlIsVisible('field', 'page_type_selector')) {
            $this->clickControl('dropdown','page_selector', false);
            $this->waitForElementVisible($xpath);
        }
        $xpath .= '/preceding-sibling::ins';
        $this->getElement($xpath)->click();
        $this->waitForPageToLoad();

        return $this;
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
        if ($this->controlIsPresent('link', 'available_themes_tab')) {
            $this->clickControl('link', 'available_themes_tab');
        }
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

    /**
     * Remove VDE block in design mode
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $block
     * @return Core_Mage_Vde_Helper
     */
    public function removeBlock($block)
    {
        $this->assertNotNull($block, 'Unable to remove block: element not exists');
        $this->addParameter('dataName', $block->attribute('data-name'));
        $this->clickControl('link', 'remove_vde_element', false);

        return $this;
    }

    /**
     * Drag and drop VDE block in design mode
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $block
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $destinationContainer
     * @return Core_Mage_Vde_Helper
     */
    public function dragBlock($block, $destinationContainer)
    {
        $this->assertNotNull($block, 'Unable to drag block: element not exists');
        $block->click();
        $this->moveto($block);
        $this->buttondown();
        $this->moveto($destinationContainer);
        $this->buttonup();
        $this->waitForPageToLoad();
        $this->assertEquals($destinationContainer->attribute('data-name'),
            $this->getContainer($block, true)->attribute('data-name')
        );

        return $this;
    }

    /**
     * Get block element by data-name or class name
     *
     * @param string $name data-name attribute in design mode or class name in navigation mode
     * @param bool $designMode
     * @return null|PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function getBlock($name, $designMode = false)
    {
        if ($designMode) {
            $mode = 'design';
            $this->addParameter('dataName', $name);
        } else {
            $mode = 'navigation';
            $this->addParameter('className', $name);
        }
        $this->setVdePage($mode);
        if ($this->controlIsPresent('fieldset', 'iframe')) {
            $this->frame('vde_container_frame');
        }
        if ($this->controlIsPresent('pageelement', 'vde_element')) {
            return $this->getElement($this->_getControlXpath('pageelement', 'vde_element'));
        }

        return null;
    }

    /**
     * Get wrapper element
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $block
     * @param bool $designMode
     * @return null|PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function getContainer($block, $designMode = false)
    {
        if ($designMode) {
            $mode = 'design';
            $this->addParameter('dataName', $block->attribute('data-name'));
        } else {
            $mode = 'navigation';
            $this->addParameter('className', $block->attribute('class'));
        }
        $this->setVdePage($mode);
        if ($this->controlIsPresent('fieldset', 'iframe')) {
            $this->frame('vde_container_frame');
        }
        $xpath = $this->_getControlXpath('pageelement', 'vde_element') . "/parent::div";
        if ($this->elementIsPresent($xpath)) {
            return $this->getElement($xpath);
        }

        return null;
    }

    /**
     * Set area and current page in design or navigation mode
     *
     * @param string $mode
     * @param string $frontPage
     */
    public function setVdePage($mode, $frontPage = '')
    {
        if ($mode == 'navigation' && $frontPage != '') {
            // Set provided frontend page as current in navigation mode
            $this->setArea('frontend');
            $this->setCurrentPage($frontPage);
        } else {
            $this->getArea() == 'admin' ? : $this->setArea('admin');
            $this->getCurrentPage() == 'vde_' . $mode ? : $this->setCurrentPage('vde_' . $mode);
        }
    }

    /**
     * Switch from VDE navigation to design mode
     *
     * @return Core_Mage_Vde_Helper
     */
    public function switchToDesignMode()
    {
        $this->window('');
        $this->clickButton('design_mode');

        return $this;
    }

    /**
     * Switch from VDE design to navigation mode
     *
     * @return Core_Mage_Vde_Helper
     */
    public function switchToNavigationMode()
    {
        $this->window('');
        $this->clickButton('navigation_mode');
        $this->waitForNewPage();
        sleep(1);

        return $this;
    }
}
