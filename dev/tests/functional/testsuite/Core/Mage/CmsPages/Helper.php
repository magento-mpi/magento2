<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsPages
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
class Core_Mage_CmsPages_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Creates page
     *
     * @param string|array $pageData
     */
    public function createCmsPage($pageData)
    {
        $pageData = $this->fixtureDataToArray($pageData);
        $this->clickButton('add_new_page');
        if (isset($pageData['page_information'])) {
            $data = $pageData['page_information'];
            if (array_key_exists('store_view', $data) && !$this->controlIsVisible('multiselect', 'store_view')) {
                unset($data['store_view']);
            }
            $this->fillTab($data, 'page_information');
        }
        if (isset($pageData['content'])) {
            if (!$this->fillContent($pageData['content'])) {
                //skip next steps, because widget insertion pop-up is opened
                return;
            }
        }
        if (isset($pageData['design'])) {
            $this->fillTab($pageData['design'], 'design');
        }
        if (isset($pageData['meta_data'])) {
            $this->fillTab($pageData['meta_data'], 'meta_data');
        }
        if (isset($cmsVars['additional_tabs'])) {
            foreach ($cmsVars['additional_tabs'] as $tabName => $data) {
                $this->fillTab($data, $tabName);
            }
        }        
        $this->saveForm('save_page');
    }

    /**
     * Fills Content tab
     *
     * @param array $content
     *
     * @return bool
     */
    public function fillContent(array $content)
    {
        $widgetsData = (isset($content['widgets'])) ? $content['widgets'] : array();
        $variableData = (isset($content['variable_data'])) ? $content['variable_data'] : array();
        $this->openTab('content');
        if (isset($content['editor']) && !$this->controlIsVisible('field', 'editor')) {
            $this->clickButton('show_hide_editor', false);
        }
        $this->fillForm($content, 'content');
        foreach ($widgetsData as $widget) {
            if (!$this->insertWidget($widget)) {
                //skip next steps, because widget insertion pop-up is opened
                return false;
            }
        }
        foreach ($variableData as $variable) {
            $this->insertVariable($variable);
        }
        return true;
    }

    /**
     * Insert widget
     *
     * @param array $widgetData
     * @param string $buttonName
     * @return bool
     */
    public function insertWidget(array $widgetData, $buttonName = 'insert_widget')
    {
        $chooseOption = (isset($widgetData['chosen_option'])) ? $widgetData['chosen_option'] : array();
        if ($this->controlIsVisible('button', $buttonName)) {
            $this->clickButton($buttonName, false);
        } elseif ($this->waitForControlEditable('link', 'wysiwyg_' . $buttonName)) {
            $this->waitForControlStopsMoving('link', 'wysiwyg_' . $buttonName);
            $this->clickControl('link', 'wysiwyg_' . $buttonName, false);
        }
        //@TODO remove when fixed bug for cms_static_block page
        try {
            $this->waitForControlVisible('dropdown', 'widget_type');
        } catch (Exception $e) {
            $this->markTestIncomplete('BUG: widget_insertion pop-up in not appears for '
                . $this->getCurrentPage() . ' page');
        }
        $this->fillFieldset($widgetData, 'widget_insertion');
        if ($chooseOption) {
            $this->selectOptionItem($chooseOption);
        }
        $this->clickButton('submit_widget_insert', false);
        //@TODO wait for widget_insertion pop-up disappear or validation message appear
        sleep(3);
        return !$this->elementIsPresent($this->_getControlXpath('fieldset', 'widget_insertion'));
    }

    /**
     * Fills selections for widget
     *
     * @param array $optionData
     */
    public function selectOptionItem($optionData)
    {
        if (!$this->controlIsVisible('button', 'select_option')) {
            $this->fail('Button \'select_option\' is not present on the page ' . $this->getCurrentPage());
        }
        $text = $this->getControlAttribute('button', 'select_option', 'text');
        $name = trim(strtolower(preg_replace('#[^a-z]+#i', '_', $text)), '_');
        $this->clickButton('select_option', false);
        $this->waitForControlVisible('fieldset', $name);
        $rowNames = array('Title', 'Product');
        $title = 'Not Selected';
        if (array_key_exists('category_path', $optionData)) {
            $nodes = explode('/', $optionData['category_path']);
            $title = end($nodes);
            $this->categoryHelper()->selectCategory($optionData['category_path'], $name);
            $this->waitForAjax();
            unset($optionData['category_path']);
        }
        if (count($optionData) > 0) {
            $xpath = $this->_getControlXpath('fieldset', 'every_popup');
            $xpathTR = $this->search($optionData, $name);
            $this->assertNotEquals(null, $xpathTR, 'Element is not found');
            $names = $this->getTableHeadRowNames($xpath . "//table[@id]");
            foreach ($rowNames as $value) {
                if (in_array($value, $names)) {
                    $this->addParameter('cellIndex', array_search($value, $names) + 1);
                    $this->addParameter('tableLineXpath', $xpathTR);
                    $text = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
                    if ($title == 'Not Selected') {
                        $title = $text;
                    } else {
                        $title = $title . ' / ' . $text;
                    }
                    break;
                }
            }
            $this->clickControl('pageelement', 'table_line_cell_index', false);
        }
        $this->checkChosenOption($title);
    }

    /**
     * Checks if the inserted item is correct
     *
     * @param string $option
     */
    public function checkChosenOption($option)
    {
        $this->addParameter('elementName', $option);
        if (!$this->controlIsPresent('pageelement', 'chosen_option_verify')) {
            $this->fail('The element ' . $option . ' was not selected');
        }
    }

    /**
     * Inserts variable
     *
     * @param string $variable
     * @param string $buttonName
     * @return void
     */
    public function insertVariable($variable, $buttonName = 'insert_variable')
    {
        if ($this->controlIsVisible('button', $buttonName)) {
            $this->clickButton($buttonName, false);
        } elseif ($this->waitForControlEditable('link', 'wysiwyg_' . $buttonName)) {
            $this->waitForControlStopsMoving('link', 'wysiwyg_' . $buttonName);
            $this->clickControl('link', 'wysiwyg_' . $buttonName, false);
        }
        $this->waitForControlVisible('fieldset', 'variable_insertion');
        $this->addParameter('variableName', $variable);
        $this->clickControl('link', 'variable', false);
    }

    /**
     * Opens CMSPage
     *
     * @param array $searchData
     */
    public function openCmsPage(array $searchData)
    {
        if (isset($searchData['filter_store_view']) && !$this->controlIsVisible('dropdown', 'filter_store_view')) {
            unset($searchData['filter_store_view']);
        }
        //Search CmsPage
        $searchData = $this->_prepareDataForSearch($searchData);
        $cmsPageLocator = $this->search($searchData, 'cms_pages_grid');
        $this->assertNotNull($cmsPageLocator, 'CMS Page is not found with data: ' . print_r($searchData, true));
        $cmsPageRowElement = $this->getElement($cmsPageLocator);
        $cmsPageUrl = $cmsPageRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Title');
        $cellElement = $this->getChildElement($cmsPageRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($cmsPageUrl));
        //Open CmsPage
        $this->url($cmsPageUrl);
        $this->validatePage('edit_cms_page');
    }

    /**
     * Deletes page
     *
     * @param array $searchPage
     */
    public function deleteCmsPage(array $searchPage)
    {
        $this->openCmsPage($searchPage);
        $this->clickButtonAndConfirm('delete_page', 'confirmation_for_delete');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////TODO
    /**
     * Validates page after creation
     *
     * @param array $pageData
     */
    public function frontValidatePage($pageData)
    {
        $this->logoutCustomer();
        $this->addParameter('url_key', $pageData['page_information']['url_key']);
        $this->addParameter('elementTitle', $pageData['page_information']['page_title']);
        if (array_key_exists('content', $pageData)) {
            if (array_key_exists('content_heading', $pageData['content'])) {
                $this->addParameter('content_heading', $pageData['content']['content_heading']);
            }
        }
        $this->frontend('test_page');
        foreach ($this->countElements($pageData) as $key => $value) {
            $actualCount = $this->getControlCount('pageelement', $key);
            $this->assertEquals($value, $actualCount, 'Count of ' . $key . ' is not ' . $value);
        }
    }

    /**
     * Count elements for validation
     *
     * @param array $pageData
     *
     * @return array
     */
    public function countElements(array $pageData)
    {
        $map = array(
            'CMS Page Link' => 'widget_cms_link',
            'CMS Static Block' => 'widget_static_block',
            'Catalog Category Link' => 'widget_category_link',
            'Catalog Product Link' => 'widget_product_link'
        );
        $resultArray = array();
        foreach ($map as $key => $value) {
            $resultArray[$value] = count($this->searchArray($pageData, $key));
        }
        return $resultArray;
    }

    /**
     * Search array recursively
     *
     * @param array $pageData
     * @param string $key
     *
     * @return array
     */
    public function searchArray($pageData, $key = null)
    {
        $found = ($key !== null) ? array_keys($pageData, $key) : array_keys($pageData);
        foreach ($pageData as $value) {
            if (is_array($value)) {
                $found = ($key !== null)
                    ? array_merge($found, $this->searchArray($value, $key))
                    : array_merge($found, $this->searchArray($value));
            }
        }
        return $found;
    }

    /**
     * Open CMSPage on frontend
     *
     * @param array $pageData
     */
    public function frontOpenCmsPage(array $pageData)
    {
        $this->addParameter('url_key', $pageData['page_information']['url_key']);
        $this->addParameter('elementTitle', $pageData['page_information']['page_title']);
        if (array_key_exists('content', $pageData)) {
            if (array_key_exists('content_heading', $pageData['content'])) {
                $this->addParameter('content_heading', $pageData['content']['content_heading']);
            }
        }
        $this->frontend('test_page');
    }
}
