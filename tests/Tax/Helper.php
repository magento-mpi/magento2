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
     * @param array $taxRateData
     */
    public function createTaxRate(array $taxRateData)
    {
        $taxRateData = $this->arrayEmptyClear($taxRateData);
        $this->clickButton('add_new_tax_rate');
        $this->fillForm($taxRateData, 'tax_rate_info');
        $this->saveForm('save_rate');
    }

    /**
     * Create new Tax rule
     *
     * @param array $taxRuleData
     */
    public function createTaxRule(array $taxRuleData)
    {
        $taxRuleData = $this->arrayEmptyClear($taxRuleData);
        $this->clickButton('add_new_tax_rule');
        $this->fillForm($taxRuleData, 'tax_rule_info');
        $this->saveForm('save_rule');
    }

    /**
     * Create Customer Tax class
     *
     * @param array|string $customerTaxClassData
     */
    public function createCustomerTaxClass($customerTaxClassData)
    {
        $this->clickButton('add_new');
        $this->fillForm($customerTaxClassData, 'create_customer_tax_class');
        $this->saveForm('save_class');
    }

    /**
     * Create Product Tax class
     *
     * @param array|string $productTaxClassData
     */
    public function createProductTaxClass($productTaxClassData)
    {
        $this->clickButton('add_new');
        $this->fillForm($productTaxClassData, 'create_product_tax_class');
        $this->saveForm('save_class');
    }

}

?>
