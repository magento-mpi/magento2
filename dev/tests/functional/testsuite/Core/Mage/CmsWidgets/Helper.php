<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsWidgets
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
class Core_Mage_CmsWidgets_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * @param $layoutUpdates
     */
    public function _layoutUpdates($layoutUpdates)
    {
        if ($layoutUpdates) {
            $this->fillLayoutUpdates($layoutUpdates);
        }
    }

    /**
     * @param $widgetOptions
     */
    protected function _widgetOptions($widgetOptions)
    {
        if ($widgetOptions) {
            $this->fillWidgetOptions($widgetOptions);
        }
    }

    /**
     * @param $widgetData
     *
     * @return array $widgetData
     */
    protected function _ifIsString($widgetData)
    {
        if (is_string($widgetData)) {
            $elements = explode('/', $widgetData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $widgetData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        return $widgetData;
    }

    /**
     * @param $widgetData
     * @return array $settings
     */
    protected function _widgetSettings($widgetData)
    {
        $settings = array();
        $settings['settings'] = (isset($widgetData['settings'])) ? $widgetData['settings'] : array();
        return $settings;
    }

    /**
     * Creates widget
     *
     * @param string|array $widgetData
     */
    public function createWidget($widgetData)
    {

        $settings = $this->_widgetSettings($widgetData);
        $frontProperties = (isset($widgetData['frontend_properties'])) ? $widgetData['frontend_properties'] : array();
        $layoutUpdates = (isset($widgetData['layout_updates'])) ? $widgetData['layout_updates'] : array();
        $widgetOptions = (isset($widgetData['widget_options'])) ? $widgetData['widget_options'] : array();

        $this->clickButton('add_new_widget_instance');
        $this->fillWidgetSettings($settings);
        if (array_key_exists('assign_to_store_views', $frontProperties)
            && !$this->controlIsPresent('multiselect', 'assign_to_store_views')
        ) {
            unset($frontProperties['assign_to_store_views']);
        }
        $this->fillFieldset($frontProperties, 'frontend_properties_fieldset');
        $this->_layoutUpdates($layoutUpdates);
        $this->_widgetOptions($widgetOptions);
        $this->saveForm('save');
    }

    /**
     * Fills settings for creating widget
     *
     * @param array $settings
     */
    public function fillWidgetSettings(array $settings)
    {
        if ($settings) {
            $this->fillDropdown('type', $settings['type']);
            $type = $this->getControlAttribute('dropdown', 'type', 'selectedValue');
            $this->addParameter('type', $type);

            list($package, $theme) = array_map('trim', (explode('/', $settings['design_package_theme'])));
            $this->addParameter('dropdownXpath', $this->_getControlXpath('dropdown', 'design_package_theme'));
            $this->addParameter('optionGroup', $package);
            $this->addParameter('optionText', $theme);
            $value = $this->getControlAttribute('pageelement', 'dropdown_group_option_text', 'value');
            $this->addParameter('package_theme', str_replace('/', '-', $value));
            $this->fillDropdown('design_package_theme', $value);
        }
        $waitCondition = array($this->_getMessageXpath('general_validation'),
                               $this->_getControlXpath('fieldset', 'layout_updates_header',
                                   $this->getUimapPage('admin', 'add_widget_options')));
        $this->clickButton('continue', false);
        $this->waitForElement($waitCondition);
        $this->validatePage('add_widget_options');
    }

    /**
     * Fills data for layout updates
     *
     * @param string|array $layoutData
     */
    public function fillLayoutUpdates(array $layoutData)
    {
        foreach ($layoutData as $value) {
            $displayOn = (array_key_exists('select_display_on', $value))
                ? $value['select_display_on']
                : '-- Please Select --';
            $chooseOptions = (array_key_exists('choose_options', $value)) ? $value['choose_options'] : array();
            $layoutIndex = $this->getControlCount('pageelement', 'layout_updates_option_boxes');
            $this->addParameter('layoutIndex', $layoutIndex);
            $this->clickButton('add_layout_update', false);
            $this->waitForElement($this->_getControlXpath('pageelement', 'layout_updates_option_box'));
            $this->fillDropdown('select_display_on', $displayOn);
            $layoutName = $this->getControlAttribute('dropdown', 'select_display_on', 'selectedValue');
            $this->addParameter('layout', $layoutName);
            $this->addParameter('widgetParam', "//div[@id='" . $layoutName . '_ids_' . $layoutIndex . "']");
            if (!empty($chooseOptions)) {
                $layout = (preg_match('/anchor_categories/', $layoutName)) ? 'categories' : 'products';
                $this->chooseLayoutOptions($value['choose_options'], $layout);
            } elseif ($this->controlIsPresent('radiobutton', 'all_categories_products_radio')) {
                $this->fillRadiobutton('all_categories_products_radio', 'Yes');
            }
            $this->fillFieldset($value, 'layout_updates_body');
        }
    }

    /**
     * Fills options for layout updates
     *
     * @param array $layoutOptions
     * @param string $layoutName
     */
    public function chooseLayoutOptions(array $layoutOptions, $layoutName = 'products')
    {
        $this->fillRadiobutton('specific_categories_products_radio', 'Yes');
        $this->clickControl('link', 'open_chooser', false);
        $this->waitForElementEditable("//div[@class='chooser']/div");
        foreach ($layoutOptions as $value) {
            if ($layoutName == 'categories') {
                $this->categoryHelper()->selectCategory($value, 'layout_updates_body');
            }
            if ($layoutName == 'products') {
                $this->searchAndChoose($value, 'layout_products_fieldset');
            }
        }
        $this->clickControl('link', 'apply', false);
        $selectedIds = explode(',', $this->getControlAttribute('field', 'specific_category', 'value'));
        $selectedIds = array_diff($selectedIds, array(''));
        $this->assertEquals(count($layoutOptions), count($selectedIds),
            'Selected number of items does not match expected');
    }

    /**
     * Fills "Widget Options" tab
     *
     * @param array $widgetOptions
     */
    public function fillWidgetOptions(array $widgetOptions)
    {
        $options = (isset($widgetOptions['chosen_option'])) ? $widgetOptions['chosen_option'] : array();
        $this->fillForm($widgetOptions, 'widgets_options');
        if ($options) {
            $this->cmsPagesHelper()->selectOptionItem($options);
        }
    }

    /**
     * Opens widget
     *
     * @param array $searchWidget
     */
    public function openWidget(array $searchWidget)
    {
        $xpathTR = $this->search($searchWidget, 'cms_widgets_grid');
        $this->assertNotNull($xpathTR, 'Widget is not found');
        $cellId = $this->getColumnIdByName('Widget Instance Title');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }

    /**
     * Deletes widget
     *
     * @param array $searchWidget
     */
    public function deleteWidget(array $searchWidget)
    {
        $this->openWidget($searchWidget);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
    }
}