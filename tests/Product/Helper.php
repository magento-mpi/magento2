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
class Product_Helper extends Mage_Selenium_TestCase
{

    public function fillProductSettings($productSettings)
    {
        $this->assertTrue($this->checkCurrentPage('new_product_settings'), 'Wrong page is displayed');
        $this->fillForm($productSettings);
        // Defining and adding %attributeSetID% and %productType% for Uimap pages.
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('product_settings');
        foreach ($productSettings as $fieldsName => $fieldValue) {
            $xpath = $fieldSet->findDropdown($fieldsName);
            switch ($fieldsName) {
                case 'attribute_set':
                    $attributeSetID = $this->getValue($xpath . "/option[text()='$fieldValue']");
                    break;
                case 'product_type':
                    $productType = $this->getValue($xpath . "/option[text()='$fieldValue']");
                    break;
                default:
                    break;
            }
        }
        $this->addParameter('attributeSetID', $attributeSetID);
        $this->addParameter('productType', $productType);
        $page->assignParams($this->_paramsHelper);
        //Steps. Сlick 'Сontinue' button
        $this->clickButton('continue_button');
    }

}