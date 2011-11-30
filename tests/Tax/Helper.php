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
class Tax_Helper extends Mage_Selenium_TestCase
{

    /**
     * Create new Tax rate
     *
     * @param array|string $taxRateData
     */
    public function createTaxRate(array $taxRateData)
    {
        if (is_string($taxRateData)) {
            $taxRateData = $this->loadData($taxRateData);
        }
        $taxRateData = $this->arrayEmptyClear($taxRateData);
        $taxTitles = (isset($taxRateData['tax_titles'])) ? $taxRateData['tax_titles'] : NULL;
        $this->clickButton('add_new_tax_rate');
        //$this->fillForm($taxRateData, 'tax_rate_info');
        $this->fillForm($taxRateData);
        $xpath = $this->_getControlXpath('fieldset', 'tax_titles');
        if ($taxTitles && $this->isElementPresent($xpath)) {
            foreach ($taxTitles as $key => $value) {
                $this->addParameter('storeNumber', $this->findTaxTitleByName($key));
                $this->fillForm(array('tax_title' => $value));
            }
        }
        $this->saveForm('save_rate');
    }

    /**
     * Search
     *
     * @param string $taxTitleData
     * @return int
     */
    public function findTaxTitleByName($taxTitleData)
    {
        $taxTitleXpath = $this->_getControlXpath('pageelement', 'tax_title_header');
        $taxTitleQty = $this->getXpathCount($taxTitleXpath);
        for ($i = 1; $i <= $taxTitleQty; $i++) {
            $text = $this->getText($taxTitleXpath . "[$i]");
            if ($text == $taxTitleData) {
                return $i;
            }
        }
        return 0;
    }

    /**
     * Create (Product\Customer)Tax Class\Rule
     *
     * @param array|string $taxItemData
     */
    public function createTaxItem($taxItemData)
    {
        if (is_string($taxItemData)) {
            $taxItemData = $this->loadData($taxItemData);
        }
        $taxItemData = $this->arrayEmptyClear($taxItemData);
        $buttons = $this->getCurrentLocationUimapPage()->getAllButtons();
        //Open form
        foreach($buttons as $buttonName => $buttonXpath) {
            if (preg_match('/add_new(_tax_rule)?$/', $buttonName)) {
                $this->clickButton($buttonName);
            }
        }
        $this->fillForm($taxItemData);
        //Save form
        $buttons = $this->getCurrentLocationUimapPage()->getAllButtons();
        foreach($buttons as $buttonName => $buttonXpath) {
            if (preg_match('/save_(rule|class)/', $buttonName)) {
                $this->saveForm($buttonName);
            }
        }
    }

    /**
     * Opens (Product\Customer)Tax Class\Rate\Rule
     *
     * @param array $taxSearchData Data for search
     * @param string $type search type (customer_tax_class|product_tax_class|tax_rates|tax_rules)
     */
    public function openTaxItem(array $taxSearchData,$type)
    {
        $taxSearchData = $this->arrayEmptyClear($taxSearchData);
        $gridName = 'manage_' . $type;
        $xpathTR = $this->search($taxSearchData,$gridName);
        $this->assertNotEquals(null, $xpathTR, 'Search item is not found');
        $elementTitle = $this->getText($xpathTR . '//td[1]');
        $this->addParameter('elementTitle', $elementTitle);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Open (Product\Customer)Tax Class\Rate\Rule and delete
     *
     * @param array $taxSearchData Data for search
     * @param string $type search type (customer_tax_class|product_tax_class|tax_rates|tax_rules)
     * @return boolean
     */
    public function deleteTaxItem(array $taxSearchData,$type)
    {
        if ($taxSearchData and $type) {
            $this->openTaxItem($taxSearchData,$type);
            $buttons = $this->getCurrentLocationUimapPage()->getAllButtons();
            foreach($buttons as $buttonName => $buttonXpath) {
                if (preg_match('/delete_(rate|class|rule)$/', $buttonName)) {
                    return $this->clickButtonAndConfirm($buttonName, 'confirmation_for_delete');
                }
            }
        }
        return false;
    }
    
}
