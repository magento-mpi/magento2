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
class Enterprise_Mage_CmsBanners_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Creates CMS Banners with required fields only
     *
     * @param string|array $pageData
     */
    public function createCmsBanner($pageData)
    {
        $pageData = $this->fixtureDataToArray($pageData);
        $this->clickButton('add_new_banner');
        if (isset($pageData['banner_properties'])) {
            $this->fillTab($pageData['banner_properties'], 'banner_properties');
        }
        if (isset($pageData['content'])) {
            $this->fillContent($pageData['content']);
        }
        if (isset($pageData['related_promotions'])) {
            $this->fillRelatedPromotions($pageData['related_promotions']);
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

        $this->fillForm($content, 'content');
        foreach ($widgetsData as $widget) {
            $button = array_key_exists('no_default_content', $content) ? 'insert_widget_content' : 'insert_widget';
            $this->cmsPagesHelper()->insertWidget($widget, $button);
        }
        foreach ($variableData as $variable) {
            $button = array_key_exists('no_default_content', $content) ? 'insert_variable_content' : 'insert_variable';
            $this->cmsPagesHelper()->insertVariable($variable, $button);
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
     * @param array $searchData
     */
    public function openCmsBanner(array $searchData)
    {
        //Search banner
        $searchData = $this->_prepareDataForSearch($searchData);
        $bannerLocator = $this->search($searchData, 'cms_banners_grid');
        $this->assertNotNull($bannerLocator, 'Cms Banner is not found with data: ' . print_r($searchData, true));
        $bannerRowElement = $this->getElement($bannerLocator);
        $bannerUrl = $bannerRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Banner');
        $cellElement = $this->getChildElement($bannerRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($bannerUrl));
        //Open banner
        $this->url($bannerUrl);
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
