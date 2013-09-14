<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UrlRewrite helper
 */
class Core_Mage_UrlRewrite_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * @param array $rewriteData
     * @param bool $isSave
     */
    public function createUrlRewrite(array $rewriteData, $isSave = true)
    {
        $this->clickButton('add_new_rewrite');
        if (isset($rewriteData['rewrite_type'])) {
            $this->fillDropdown('rewrite_type', $rewriteData['rewrite_type']);
            $this->waitForPageToLoad();
            $this->validatePage();
        }
        if (isset($rewriteData['product_search'])) {
            $this->selectProduct($rewriteData['product_search']);
        }
        if (isset($rewriteData['select_category'])) {
            $this->selectCategory($rewriteData['select_category']['category']);
            $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
            $this->validatePage();
        } elseif ($this->getCurrentPage() == 'new_url_rewrite_product_category_selection') {
            $this->clickButton('skip_category_selection');
        }
        if (isset($rewriteData['rewrite_info']['store']) && !$this->controlIsVisible('dropdown', 'store')) {
            unset($rewriteData['store']);
        }
        if (isset($rewriteData['rewrite_info'])) {
            $this->fillFieldset($rewriteData['rewrite_info'], 'url_rewrite_info');
        }
        if ($isSave) {
            $this->saveForm('save');
        }
    }

    /**
     * Select category by path. Category names of one level must be unique
     *
     * @param string $categoryPath
     * @param string $fieldsetName
     */
    public function selectCategory($categoryPath, $fieldsetName = 'select_category')
    {
        $this->waitForControlEditable('fieldset', $fieldsetName);
        $categoriesOnTheWay = explode('/', $categoryPath);
        $currentCategoryIndex = 0;
        $maxIndexToUnfold = count($categoriesOnTheWay) - 1;
        while ($currentCategoryIndex < $maxIndexToUnfold) {
            $this->addParameter('categoryName', $categoriesOnTheWay[$currentCategoryIndex + 1]);
            if (!$this->controlIsVisible(self::FIELD_TYPE_LINK, 'category_name')) {
                $this->addParameter('categoryName', $categoriesOnTheWay[$currentCategoryIndex]);
                $foldingIcon = $this->getControlElement(self::FIELD_TYPE_LINK, 'category_folding_icon');
                $foldingIcon->click();
                $this->waitForControlVisible(self::FIELD_TYPE_LINK, 'category_name');
            }
            $currentCategoryIndex++;
        }
        $this->addParameter('categoryName', $categoriesOnTheWay[$currentCategoryIndex]);
        $category = $this->getControlElement(self::FIELD_TYPE_LINK, 'category_name');
        $category->click();
        $this->pleaseWait();
    }

    /**
     * Select product
     *
     * @param array $searchData
     */
    public function selectProduct(array $searchData)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $productLocator = $this->search($searchData, 'product_search');
        $this->assertNotNull($productLocator, 'Product is not found with data: ' . print_r($searchData, true));
        $productRowElement = $this->getElement($productLocator);
        $productUrl = $productRowElement->attribute('title');
        $this->addParameter('productId', $this->defineParameterFromUrl('product', $productUrl));
        $this->url($productUrl);
        $this->validatePage('new_url_rewrite_product_category_selection');
    }

    /**
     * Open url rewrite
     *
     * @param array $searchData
     */
    public function openUrlRewrite(array $searchData)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $rewriteLocator = $this->search($searchData, 'url_rewrite_grid');
        $this->assertNotNull($rewriteLocator, 'Url Rewrite is not found with data: ' . print_r($searchData, true));
        $rewriteRowElement = $this->getElement($rewriteLocator);
        $rewriteUrl = $rewriteRowElement->attribute('title');
        $this->addParameter('id', $this->defineIdFromUrl($rewriteUrl));
        $this->url($rewriteUrl);
        $this->validatePage('edit_url_rewrite');
    }
}
