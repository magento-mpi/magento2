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
class CmsPages_Helper extends Mage_Selenium_TestCase
{

    /**
     * Creates page
     *
     * @param string|array $pageData
     */
    public function createPage($pageData)
    {
        if (is_string($pageData)) {
            $pageData = $this->loadData($pageData);
        }
        $pageData = $this->arrayEmptyClear($pageData);
        $pageInfo = (isset($pageData['page_information'])) ? $pageData['page_information'] : NULL;
        $content = (isset($pageData['content'])) ? $pageData['content'] : NULL;
        $design = (isset($pageData['design'])) ? $pageData['design'] : NULL;
        $metaData = (isset($pageData['meta_data'])) ? $pageData['meta_data'] : NULL;
        $this->clickButton('add_new_page');
        if ($pageInfo) {
            $this->clickControl('tab', 'page_information', FALSE);
            $this->fillPageInfo($pageInfo);
        }
        if ($content) {
            $this->fillContent($content);
        }
        if ($design) {
            $this->clickControl('tab', 'design', FALSE);
            $this->fillForm($design);
        }
        if ($metaData) {
            $this->clickControl('tab', 'meta_data', FALSE);
            $this->fillForm($widgetOptions);
        }
        $this->saveForm('save_page');
    }

    /**
     * Fills Page Information tab
     *
     * @param array $pageInfo
     * @param bool $validate
     */
    public function fillPageInfo(array $pageInfo, $validate = FALSE)
    {
        if ($this->controlIsPresent('multiselect', 'store_view') && $validate == FALSE) {
            if (!array_key_exists('store_view', $pageInfo)) {
                $pageInfo['store_view'] = 'All Store Views';
            }
        } elseif (!$this->controlIsPresent('multiselect', 'store_view') && $validate == FALSE) {
            if (array_key_exists('store_view', $pageInfo)) {
                unset($pageInfo['store_view']);
            }
        }
        $this->fillForm($pageInfo);
    }

    /**
     * Fills Content tab
     *
     * @param string|array $content
     */
    public function fillContent($content)
    {
        if (is_string($content)) {
            $content = $this->loadData($content);
        }
        $content = $this->arrayEmptyClear($content);
        $this->clickControl('tab', 'content', FALSE);
        $this->fillForm($content);
        if (array_key_exists('widgets', $content)) {
            $this->insertWidget($content['widgets']);
        }
        if (array_key_exists('variable', $content)) {
            $this->insertVariable($content['variable']);
        }
    }

    /**
     * Inserts widgets
     *
     * @param array $widgets
     */
    public function insertWidget(array $widgets)
    {
        if (!$this->controlIsPresent('field', 'editor')) {
            $this->clickButton('show_hide_editor', FALSE);
        }
        foreach ($widgets as $key => $value) {
            $options = (isset($value['chosen_option'])) ? $value['chosen_option'] : null;
            $this->clickButton('insert_widget', FALSE);
            $this->waitForAjax();
            $this->fillForm($value);
            if ($options) {
                $this->cmsWidgetsHelper()->fillSelectOption($options);
            }
            $this->clickButton('submit_widget_insert', FALSE);
            $this->waitForAjax();
        }
    }

    /**
     * Inserts variable
     *
     * @param string $variable
     */
    public function insertVariable($variable)
    {
        if (!$this->controlIsPresent('field', 'editor')) {
            $this->clickButton('show_hide_editor', FALSE);
        }
        $this->clickButton('insert_variable', FALSE);
        $this->waitForAjax();
        $this->addParameter('variableName', $variable);
        $this->clickControl('link', 'variable', FALSE);
    }

    /**
     * Validates page after creation
     *
     * @param array $pageData
     */
    public function frontValidatePage($pageData)
    {
        $this->logoutCustomer();
        $this->addParameter('url_key', $pageData['page_information']['url_key']);
        $this->addParameter('page_title', $pageData['page_information']['page_title']);
        $this->addParameter('content_heading', $pageData['content']['content_heading']);
        $this->frontend('test_page');
        foreach ($this->countElements($pageData) as $key => $value) {
            $xpath = $this->_getControlXpath('pageelement', $key);
            $this->assertTrue($this->getXpathCount($xpath) == $value);
        }
    }

    /**
     * Count elements for validation
     *
     * @param array $pageData
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
     * @return array
     */
    function searchArray($pageData, $key = NULL){

        $found = ($key !== NULL ? array_keys($pageData, $key) : array_keys($pageData));
        foreach($pageData as $value){
            if(is_array($value)){
                $found = ($key !== NULL ? array_merge($found, $this->searchArray($value, $key))
                        : array_merge($found, $this->searchArray($value)));
            }
        }
        return $found ;
    }

    /**
     * Opens page
     *
     * @param array $searchPage
     */
    public function openPage(array $searchPage)
    {
        $this->_prepareDataForSearch($searchPage);
        $xpathTR = $this->search($searchPage, 'cms_pages_grid');
        $this->assertNotEquals(NULL, $xpathTR, 'Page is not found');
        $names = $this->shoppingCartHelper()->getColumnNamesAndNumbers('page_grid_head', FALSE);
        if (array_key_exists('Title', $names)) {
            $text = $this->getText($xpathTR . '//td[' . $names['Title'] . ']');
            $this->addParameter('pageName', $text);
        }
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Deletes page
     *
     * @param array $searchPage
     */
    public function deletePage(array $searchPage)
    {
        $searchPage = $this->arrayEmptyClear($searchPage);
        if (!empty($searchPage)) {
            $this->openWidget($searchPage);
            $this->answerOnNextPrompt('OK');
            $this->clickButton('delete_page');
            $this->assertTrue($this->checkMessage('successfully_deleted_page'), 'The page has not been deleted');
        }
    }
}
