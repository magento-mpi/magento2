<?php
/**
 * Helper class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_CmsPages_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * @param $pageData
     * @return array
     */
    protected function _verifyCmsPageVars($pageData)
    {
        $cmsVars = array();
        $cmsVars['pageInfo'] = (isset($pageData['page_information'])) ? $pageData['page_information'] : array();
        $cmsVars['content'] = (isset($pageData['content'])) ? $pageData['content'] : array();
        $cmsVars['design'] = (isset($pageData['design'])) ? $pageData['design'] : array();
        $cmsVars['metaData'] = (isset($pageData['meta_data'])) ? $pageData['meta_data'] : array();
        $cmsVars['additional_tabs'] = (isset($pageData['additional_tabs'])) ? $pageData['additional_tabs'] : array();
        return $cmsVars;
    }

    /**
     * @param $cmsVars
     */
    protected function _cmsFillTab($cmsVars)
    {
        if (isset($cmsVars['metaData'])) {
            $this->fillTab($cmsVars['metaData'], 'meta_data');
        }
    }

    /**
     * Creates page
     *
     * @param string|array $pageData
     */
    public function createCmsPage($pageData)
    {
        $pageData = $this->fixtureDataToArray($pageData);
        $cmsVars = $this->_verifyCmsPageVars($pageData);
        $this->clickButton('add_new_page');
        if ($cmsVars['pageInfo']) {
            if (array_key_exists('store_view', $cmsVars['pageInfo'])
                && !$this->controlIsPresent('multiselect', 'store_view')) {
                unset($cmsVars['pageInfo']['store_view']);
            }
            $this->fillTab($cmsVars['pageInfo'], 'page_information');
        }
        if ($cmsVars['content']) {
            if (!$this->fillContent($cmsVars['content'])) {
                //skip next steps, because widget insertion pop-up is opened
                return;
            }
        }
        if ($cmsVars['design']) {
            $this->fillTab($cmsVars['design'], 'design');
        }
        $this->_cmsFillTab($cmsVars['metaData']);
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
     *
     * @return bool
     */
    public function insertWidget(array $widgetData, $button)
    {
        $chooseOption = (isset($widgetData['chosen_option'])) ? $widgetData['chosen_option'] : array();
        if ($this->controlIsPresent('fieldset', 'wysiwyg_editor_buttons')) {
            $this->clickControl('link', 'wysiwyg_insert_widget', false);
        } else {
            $this->clickButton($button, false);
        }
        $this->waitForControlVisible('dropdown', 'widget_type');
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
        $name = '';
        if ($this->controlIsPresent('button', 'select_option')) {
            $text = $this->getControlAttribute('button', 'select_option', 'text');
            $name = trim(strtolower(preg_replace('#[^a-z]+#i', '_', $text)), '_');
            $this->clickButton('select_option', false);
            $this->waitForElement($this->_getControlXpath('fieldset', $name));
        } else {
            $this->fail('Button \'select_option\' is not present on the page ' . $this->getCurrentPage());
        }

        $rowNames = array('Title', 'Product Name');
        $title = 'Not Selected';
        $xpath = $this->_getControlXpath('fieldset', $name);
        if (array_key_exists('category_path', $optionData)) {
            $this->addParameter('widgetParam', $xpath);
            $nodes = explode('/', $optionData['category_path']);
            $title = end($nodes);
            $this->categoryHelper()->selectCategory($optionData['category_path'], $name);
            $this->waitForAjax();
            unset($optionData['category_path']);
        }
        if (count($optionData) > 0) {
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
     */
    public function insertVariable($variable, $button = 'insert_variable')
    {
        if ($this->controlIsPresent('link', 'wysiwyg_insert_variable')) {
            $this->clickControl('link', 'wysiwyg_insert_variable', false);
        } else {
            $this->clickButton($button, false);
        }
        $this->waitForElement($this->_getControlXpath('fieldset', 'variable_insertion'));
        $this->addParameter('variableName', $variable);
        $this->clickControl('link', 'variable', false);
    }

    /**
     * Opens CMSPage
     *
     * @param array $searchPage
     */
    public function openCmsPage(array $searchPage)
    {
        if (array_key_exists('filter_store_view', $searchPage)
            && !$this->controlIsPresent('dropdown', 'filter_store_view')
        ) {
            unset($searchPage['filter_store_view']);
        }
        $xpathTR = $this->search($searchPage, 'cms_pages_grid');
        $this->assertNotEquals(null, $xpathTR, 'CMS Page is not found');
        $cellId = $this->getColumnIdByName('Title');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
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
        $map = array('CMS Page Link'         => 'widget_cms_link', 'CMS Static Block' => 'widget_static_block',
                     'Catalog Category Link' => 'widget_category_link',
                     'Catalog Product Link'  => 'widget_product_link');
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
