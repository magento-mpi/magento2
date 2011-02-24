<?php

/**
 * Model_Admin_Category model
 *
 * @author Magento Inc.
 */
class Model_Admin_Category extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->Data = array(); //Core::getEnvConfig('backend/categories');
    }

    /**
     * Define Correct Sub Category 
     *
     * @param string $rootCat
     * @param string $subCat
     * @return string
     */
    public function defineCorrectSubCategory($rootCat, $subCat)
    {
        preg_match_all("/id='(.*)'/", $rootCat, $result);
        $rootID = $result[1][0];
        $isDiscloseCategory = "//div[contains(a/span/@id,'" . $rootID .
                "')]/img[contains(@class,'x-tree-ec-icon x-tree-elbow-plus')
                    or contains(@class,'x-tree-ec-icon x-tree-elbow-end-plus')]";
        if ($this->isElementPresent($isDiscloseCategory)) {
            $this->click($isDiscloseCategory);
            $this->pleaseWait();
        }
        $link = $rootCat . '/ancestor::li/ul/li[contains(div/a/span,"' . $subCat . '")]';
        $categoryText = '/div/a/span';
        $qtyCat = $this->getXpathCount($link . $categoryText);

        $isCorrectName = array();
        for ($i = 1; $i <= $qtyCat; $i++) {
            $text = $this->getText($link . "[$i]" . $categoryText);
            $text = preg_replace('/ \([0-9]+\)/', '', $text);
            if ($subCat == $text) {
                $categoryID = $this->getAttribute($link . "[$i]" . $categoryText . '@id');
                $isCorrectName[] = "//*[@id='" . $categoryID . "']";
            }
        }
        return $isCorrectName;
    }

    /**
     * Define Correct Root Category
     *
     * @param string $rootCat
     * @return string
     */
    public function defineCorrectRoot($rootCat)
    {
        $categoryText = '/div/a/span';
        $link = '//ul/div/li[contains(div/a/span,"' . $rootCat . '")]';
        $qtyCat = $this->getXpathCount($link . $categoryText);
        $isCorrectName = array();
        for ($i = 1; $i <= $qtyCat; $i++) {
            $text = $this->getText($link . "[$i]" . $categoryText);
            $text = preg_replace('/ \([0-9]+\)/', '', $text);
            if ($rootCat == $text) {
                $categoryID = $this->getAttribute($link . "[$i]" . $categoryText . '@id');
                $isCorrectName[] = "//*[@id='" . $categoryID . "']";
            }
        }
        return $isCorrectName;
    }

    /**
     * Select categoty
     *
     * @param string $categotyPath
     * @return boolean
     */
    public function doSelectCategory($categotyPath)
    {
        if ($categotyPath != NULL) {
            $result = FALSE;
            $link = '/';
            $categoryText = '/div/a/span';
            $isDiscloseCategory = "/div/img[normalize-space(@class)='x-tree-ec-icon x-tree-elbow-plus']";
            $nodes = explode('/', $categotyPath);
            $rootCat = array_shift($nodes);
            $correctRoot = $this->defineCorrectRoot($rootCat);
            foreach ($nodes as $value) {
                $correctSubCat = array();
                for ($i = 0; $i < count($correctRoot); $i++) {
                    $correctSubCat = array_merge($correctSubCat,
                                    $this->defineCorrectSubCategory($correctRoot[$i], $value));
                }
                $correctRoot = $correctSubCat;
            }
            if (count($correctRoot) > 0) {
                if (count($correctRoot) > 1) {
                    $this->printInfo('On the specified path there are ' . count($correctRoot) .
                            ' categories.To be selected first');
                }
                $this->click(array_shift($correctRoot));
                $this->pleaseWait();
                if (count($nodes) > 0) {
                    $pageName = end($nodes);
                } else {
                    $pageName = $rootCat;
                }
                $openedPageName = $this->getText("//*[@id='category-edit-container']//h3");
                $openedPageName = preg_replace('/ \(ID\: [0-9]+\)/', '', $openedPageName);
                if ($pageName == $openedPageName) {
                    return TRUE;
                } else {
                    $this->printInfo('Opened the wrong category');
                    $this->printInfo($pageName);
                    $this->printInfo($openedPageName);
                }
            } else {
                $this->setVerificationErrors("$categotyPath page could not be selected");
            }
            return $result;
        } else {
            return TRUE;
        }
    }

    /**
     * Fill General Information Tab
     *
     * @param <type> $params
     */
    public function fillGeneralInformation($params)
    {
        $this->setUiNamespace('admin/pages/catalog/categories/manage_categories');
        $isActive = $this->getAttribute($this->getUiElement('tabs/general_information') . '@class');
        if ($isActive != 'tab-item-link active') {
            $this->click($this->getUiElement('tabs/general_information'));
        }
        $this->checkAndFillField($params, 'category_name', NULL);
        $this->checkAndSelectField($params, 'category_is_active');
        $this->checkAndFillField($params, 'category_url', NULL);
        $this->checkAndFillField($params, 'category_description', NULL);
        $this->checkAndFillField($params, 'category_page_title', NULL);
        $this->checkAndFillField($params, 'category_meta_keywords', NULL);
        $this->checkAndFillField($params, 'category_meta_description', NULL);
        $this->checkAndSelectField($params, 'category_include_in_menu');
    }

    /**
     * Fill Display Settings
     *
     * @param <type> $params
     */
    public function fillDisplaySettings($params)
    {
        $this->setUiNamespace('admin/pages/catalog/categories/manage_categories');
        $isActive = $this->getAttribute($this->getUiElement('tabs/display_settings') . '@class');
        if ($isActive != 'tab-item-link active') {
            $this->click($this->getUiElement('tabs/display_settings'));
        }
        $this->checkAndSelectField($params, 'category_display_mode');
        $this->checkAndSelectField($params, 'category_cms_block');
        $this->checkAndSelectField($params, 'category_is_anchor');
    }

    /**
     * Fill Custom Design
     *
     * @param <type> $params
     */
    public function fillCustomDesign($params)
    {
        //$Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manage_categories');
        $isActive = $this->getAttribute($this->getUiElement('tabs/custom_design') . '@class');
        if ($isActive != 'tab-item-link active') {
            $this->click($this->getUiElement('tabs/custom_design'));
        }
        $this->checkAndSelectField($params, 'category_use_parent_settings');
        $this->checkAndSelectField($params, 'category_apply_to_products');
        $this->checkAndSelectField($params, 'category_custom_design');
        $this->checkAndFillField($params, 'category_custom_design_from', NULL);
        $this->checkAndFillField($params, 'category_ustom_design_to', NULL);
        $this->checkAndSelectField($params, 'category_page_layout');
        $this->checkAndFillField($params, 'category_custom_layout_update', NULL);
    }

    /**
     * Create Category
     *
     * @param <type> $params
     */
    public function doCreateCategory($params)
    {
        //$Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manage_categories');
        $this->click($this->getUiElement('buttons/add_sub_category'));
        $this->pleaseWait();
        $this->fillGeneralInformation($params);
        $this->fillDisplaySettings($params);
        $this->fillCustomDesign($params);
        $this->saveAndVerifyForErrors();
    }

    /**
     * Delete Category
     *
     * @param <type> $categotyPath
     */
    public function doDeleteCategory($categotyPath)
    {
        if ($this->doSelectCategory($categotyPath)) {
            $this->doDeleteElement();
        }
    }

}