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
class CmsWidgets_Helper extends Mage_Selenium_TestCase
{

    private static $LAYOUTS = array(
        'anchor_categories' => 'Anchor Categories',
        'notanchor_categories' => 'Non-Anchor Categories',
        'all_products' => 'All Product Types',
        'simple_products' => 'Simple Product',
        'grouped_products' => 'Grouped Product',
        'configurable_products' => 'Configurable Product',
        'virtual_products' => 'Virtual Product',
        'bundle_products' => 'Bundle Product',
        'downloadable_products' => 'Downloadable Product',
        'all_pages' => 'All Pages',
        'pages' => 'Specified Page'
    );
    private static $TYPES = array(
        'cms-widget_page_link' => 'CMS Page Link',
        'cms-widget_block' => 'CMS Static Block',
        'catalog-category_widget_link' => 'Catalog Category Link',
        'catalog-product_widget_new' => 'Catalog New Products List',
        'catalog-product_widget_link' => 'Catalog Product Link',
        'sales-widget_guest_form' => 'Orders and Returns',
        'reports-product_widget_compared' => 'Recently Compared Products',
        'reports-product_widget_viewed' => 'Recently Viewed Products'
    );

    /**
     * Creates widget
     *
     * @param string|array $widgetData
     */
    public function createWidget($widgetData)
    {
        if (is_string($widgetData)) {
            $widgetData = $this->loadData($widgetData);
        }
        $widgetData = $this->arrayEmptyClear($widgetData);
        $settings = (isset($widgetData['settings'])) ? $widgetData['settings'] : NULL;
        $frontProperties = (isset($widgetData['frontend_properties'])) ? $widgetData['frontend_properties'] : NULL;
        $layoutUpdates = (isset($widgetData['layout_updates'])) ? $widgetData['layout_updates'] : NULL;
        $widgetOptions = (isset($widgetData['widget_options'])) ? $widgetData['widget_options'] : NULL;

        if ($settings) {
            $this->clickButton('add_new_widget_instance');
            $this->fillSettings($settings);
        }
        if ($frontProperties) {
            $this->fillForm($frontProperties);
        }
        if ($layoutUpdates) {
            $this->fillLayoutUpdates($layoutUpdates);
        }
        if ($widgetOptions) {
            $this->fillWidgetOptions($widgetOptions);
        }
        $this->saveForm('save');
    }

    /**
     * Fills settings for creating widget
     *
     * @param string|array $settings
     */
    public function fillSettings($settings)
    {
        if (is_string($settings)) {
            $settings = $this->loadData($settings);
            $settings = $this->arrayEmptyClear($settings);
        }
        $type = FALSE;
        foreach (self::$TYPES as $key => $value) {
            $type = (preg_match('/^' . $settings['type'] . '/', $value)) ? $key : $type;
        }
        if ($type != FALSE) {
            $this->addParameter('type', $type);
        } else {
            $this->fail('Could not map ' . $settings['type'] . ' with any type of widget');
        }
        $packageTheme = explode('/', $settings['design_package_theme']);
        $this->addParameter('package', $packageTheme[0]);
        $this->addParameter('theme', $packageTheme[1]);
        $this->fillForm($settings);
        $this->clickButton('continue');
    }

    /**
     * Fills data for layout updates
     *
     * @param string|array $layoutData
     */
    public function fillLayoutUpdates($layoutData)
    {
        if (is_string($layoutData)) {
            $layoutData = $this->loadData($layoutData);
            $layoutData = $this->arrayEmptyClear($layoutData);
        }
        $count = 0;
        foreach ($layoutData as $key => $value) {
            $layoutName = FALSE;
            foreach (self::$LAYOUTS as $layout => $data) {
                $layoutName = (preg_match('/^' . $value['select_display_on'] . '/', $data)) ? $layout : $layoutName;
            }
            if ($layoutName != FALSE) {
                $this->addParameter('layout', $layoutName);
                $this->addParameter('param', "//div[@id='" . $layoutName . '_ids_' . $count . "']");
                $this->addParameter('index', $count++);
                $this->clickButton('add_layout_update', FALSE);
                $this->fillForm($value);
                $xpathOptionsAll = $this->_getControlXpath('radiobutton', 'all_categories_products_radio');
                if (array_key_exists('choose_options', $value)) {
                    if (preg_match('/anchor_categories/', $layoutName)) {
                        $this->chooseLayoutOptions($value['choose_options'], 'categories');
                    } else {
                        $this->chooseLayoutOptions($value['choose_options']);
                    }
                } else {
                    if ($this->isElementPresent($xpathOptionsAll)) {
                        $this->check($xpathOptionsAll);
                    }
                }
            } else {
                $this->fail('Could not map ' . $value['select_display_on'] . ' with any type of layout');
            }
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
        $this->clickControl('radiobutton', 'specific_categories_products_radio', FALSE);
        $this->clickControl('link', 'open_chooser', FALSE);
        $this->pleaseWait();
        switch ($layoutName) {
            case 'categories':
                foreach ($layoutOptions as $key => $value) {
                    $this->selectCategory($value);
                }
                break;
            default:
                foreach ($layoutOptions as $key => $value) {
                    $this->searchAndChoose(array($key => $value));
                }
                break;
        }
        $this->clickControl('link', 'apply', FALSE);
    }

    /**
     * Fills "Widget Options" tab
     *
     * @param string|array $widgetOptions
     */
    public function fillWidgetOptions($widgetOptions)
    {
        if (is_string($widgetOptions)) {
            $widgetOptions = $this->loadData($widgetOptions);
            $widgetOptions = $this->arrayEmptyClear($widgetOptions);
        }
        $this->clickControl('tab', 'widgets_options', FALSE);
        $this->fillForm($widgetOptions);
        $type = explode('/', $this->getCurrentLocationUimapPage()->getMca());
        if (array_key_exists('chosen_option', $widgetOptions)) {
            $options = $widgetOptions['chosen_option'];
            switch ($type[3]) {
                case 'cms-widget_page_link':
                    $this->clickButton('select_page', FALSE);
                    $this->pleaseWait();
                    $this->searchAndOpen(array('filter_url_key' => $options['filter_url_key']), FALSE);
                    $this->checkChosenOption($options['title']);
                    break;
                case 'cms-widget_block':
                    $this->clickButton('select_block', FALSE);
                    $this->pleaseWait();
                    $this->searchAndOpen(array('filter_identifier' => $options['filter_identifier']), FALSE);
                    $this->checkChosenOption($options['title']);
                    break;
                case 'catalog-category_widget_link':
                    $this->clickButton('select_category', FALSE);
                    $this->pleaseWait();
                    $this->addParameter('param', "//div[@id='widget-chooser_content']");
                    $this->selectCategory($options['category_path']);
                    $this->checkChosenOption($options['title']);
                    break;
                case 'catalog-product_widget_link':
                    $this->clickButton('select_product', FALSE);
                    $this->pleaseWait();
                    if (array_key_exists('category_path', $options)) {
                        $this->addParameter('param', "//div[@id='widget-chooser_content']");
                        $this->selectCategory($options['category_path']);
                        $this->waitForAjax();
                    }
                    $this->searchAndOpen(array('filter_sku' => $options['filter_sku']), FALSE);
                    $this->checkChosenOption($options['title']);
                    break;
            }
        }
    }

    /**
     * Checks if the inserted item is correct
     *
     * @param string $option
     */
    public function checkChosenOption($option)
    {
        $this->addParameter('elementName', $option);
        $xpathOption = $this->_getControlXpath('pageelement', 'chosen_option');
        if (!$this->isElementPresent($xpathOption)) {
            $this->fail('The element ' . $option . ' was not selected');
        }
    }

    /**
     * Selects the category
     *
     * @param string $categoryPath
     */
    public function selectCategory($categoryPath)
    {
        $nodes = explode('/', $categoryPath);
        $rootCat = array_shift($nodes);
        $correctRoot = $this->categoryHelper()->defineCorrectCategory($rootCat);
        foreach ($nodes as $value) {
            $correctSubCat = array();
            foreach ($correctRoot as $v) {
                $correctSubCat = array_merge($correctSubCat,
                        $this->categoryHelper()->defineCorrectCategory($value, $v));
            }
            $correctRoot = $correctSubCat;
        }
        if ($correctRoot) {
            $this->click('//*[@id=\'' . array_shift($correctRoot) . '\']');
            if ($nodes) {
                $pageName = end($nodes);
            } else {
                $pageName = $rootCat;
            }
        } else {
            $this->fail("Category with path='$categoryPath' could not be selected");
        }
    }

    /**
     * Opens widget
     *
     * @param array $searchWidget
     */
    public function openWidget(array $searchWidget)
    {
        $this->assertTrue($this->searchAndOpen($searchWidget), 'Widget is not found');
    }

    /**
     * Deletes widget
     *
     * @param array $searchWidget
     */
    public function deleteWidget(array $searchWidget)
    {
        $searchWidget = $this->arrayEmptyClear($searchWidget);
        if (!empty($searchWidget)) {
            $this->openWidget($searchWidget);
            $this->answerOnNextPrompt('OK');
            $this->clickButton('delete');
            $this->assertTrue($this->checkMessage('successfully_deleted_widget'), 'The widget has not been deleted');
        }
    }

}
