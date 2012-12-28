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

    /**
     * Search for removable/draggable element in container and return its name
     *
     * @param string $operation Operation with element (draggable/removable)
     * @param string|null $wrapperDataName
     * @return string|null
     */
    public function getOperableBlockName($operation = 'draggable', $wrapperDataName = null)
    {
        $locator = $this->_getControlXpath('pageelement', $operation . '_elements');
        if ($wrapperDataName) {
            $this->addParameter('dataName', $wrapperDataName);
            if ($this->controlIsPresent('pageelement', 'vde_element')) {
                $locator = $this->_getControlXpath('pageelement', 'vde_element') . $locator;
            }
        }
        if ($this->elementIsPresent($locator)) {
            $elements = $this->getElements($locator);
            /** @var $block PHPUnit_Extensions_Selenium2TestCase_Element */
            $block = $elements[0];
            return $block->attribute('data-name');
        }

        return null;
    }

    /**
     * Remove VDE block in design mode
     *
     * @param string $name
     * @return Core_Mage_Vde_Helper
     */
    public function removeBlock($name)
    {
        $this->addParameter('dataName', $name);
        $this->clickControl('link', 'remove_vde_element', false);

        return $this;
    }

    /**
     * Drag and drop VDE block in design mode
     *
     * @param string $blockName
     * @param string $destinationElementName
     * @return Core_Mage_Vde_Helper
     */
    public function dragBlock($blockName, $destinationElementName)
    {
        $this->addParameter('dataName', $blockName);
        $block = $this->getElement($this->_getControlXpath('pageelement', 'vde_element'));

        $this->addParameter('dataName', $destinationElementName);
        $destinationElement = $this->getElement($this->_getControlXpath('pageelement', 'vde_element'));

        $block->click();
        $this->moveto($block);
        $this->buttondown();
        $this->moveto($destinationElement);
        $this->buttonup();

        return $this;
    }
}
