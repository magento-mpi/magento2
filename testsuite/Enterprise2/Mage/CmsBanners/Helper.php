<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsBanners
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
class Enterprise2_Mage_CmsBanners_Helper extends Mage_Selenium_TestCase
{
    /**
     * Creates CMS Banners with required fields only
     *
     * @param string|array $pageData
     */
    public function createCmsBanner($pageData)
    {
        if (is_string($pageData)) {
            $elements = explode('/', $pageData);
            $fileName = (count($elements) > 1) ? array_shift($elements): '';
            $pageData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $bannerProperties = (isset($pageData['banner_properties'])) ? $pageData['banner_properties'] : array();
        $content = (isset($pageData['content'])) ? $pageData['content'] : array();
        $relatedPromotions = (isset($pageData['related_promotions'])) ? $pageData['related_promotions'] : array();
        $this->clickButton('add_new_banner');
        $this->fillTab($bannerProperties, 'banner_properties');

        if ($content) {
            $this->fillContent($content);
        }
        if ($relatedPromotions) {
            $this->fillRelatedPromotions($relatedPromotions);
        }
        $this->saveForm('save_banner');
    }

    /**
     * Fills Content tab
     *
     * @param array $content
     */
    public function fillContent(array $content)
    {
        $widgetsData = (isset($content['widgets'])) ? $content['widgets'] : array();
        $variableData = (isset($content['variable_data'])) ? $content['variable_data'] : array();

        $this->fillTab($content, 'content');
        foreach ($widgetsData as $widget) {
            $buttonName = array_key_exists('no_default_content', $content) ? 'insert_widget_content' : 'insert_widget';
            $this->cmsPagesHelper()->insertWidget($widget, $buttonName);
        }
        foreach ($variableData as $variable) {
            $buttonName = array_key_exists('no_default_content', $content) ? 'insert_variable_content' : 'insert_variable';
            $this->cmsPagesHelper()->insertVariable($variable, $buttonName);
        }
    }

    /**
     * Select Catalog Price Rule and Shopping Cart Price Rules
     *
     * @param array $content
     */
    public function fillRelatedPromotions(array $content)
    {
        $this->openTab('related_promotions');
        $this->searchAndChoose(array('related_catalog_rules_name' => $content['catalog_rule']),
            'related_catalog_price_rules');
        $this->searchAndChoose(array('related_shopping_cart_rule_name' => $content['price_rule']),
            'related_shopping_cart_price_rules');
    }

    /**
     * Open CMS Banner
     *
     * @param array $searchPage
     */
    public function openCmsBanner(array $searchPage)
    {
        $xpathTR = $this->search($searchPage, 'cms_banners_grid');
        $this->assertNotEquals(null, $xpathTR, 'CMS Banner is not found');
        $cellId = $this->getColumnIdByName('Banner Name');
        $this->addParameter('bannerName', $this->getText($xpathTR . '//td[' . $cellId . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $cellId . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Delete CMS Banner
     *
     * @param array $searchPage
     */
    public function deleteCmsBanner(array $searchPage)
    {
        $this->openCmsBanner($searchPage);
        $this->clickButtonAndConfirm('delete_banner', 'confirmation_for_delete');
    }

}
