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
 * @author      Magento Goext Team <DL-Magento-Team-Goext@corp.ebay.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Unitprice_SetupBeforeSuiteTest
    extends Mage_Selenium_TestCase
{
    /**
     * @test
     */
    public function beforeTest()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');

        //Configure Unit Price
        $configData = $this->loadDataSet('ConfigUnitPrice', 'unitprice_default_sysconf');
        $this->systemConfigurationHelper()->configure($configData);

        //Create attribute for Configurable Product
        $this->navigate('manage_attributes');

        $attrData = $this->loadDataSet(
            'ProductAttributes', 'product_attribute_dropdown_with_options',
            array(
                'attribute_code' => 'goext_test_attribute',
                'admin_title' => 'general_dropdown_goext',
                'option_1' => array(
                    'is_default' => 'No',
                    'admin_option_name' => 'goext'
                ),
                'option_2' => array(
                    'is_default' => 'No',
                    'admin_option_name' => 'autotest'
                ),
                'option_3' => array(
                    'is_default' => 'No',
                    'admin_option_name' => 'autotest2'
                ),
            )
        );
        $attrForSearch =
            array(
                'attribute_code' => 'goext_test_attribute',
                'attribute_label' => 'general_dropdown_goext'
            );
        $this->_prepareDataForSearch($attrForSearch);
        $xpathTR = $this->search($attrForSearch, 'attributes_grid');
        if ($xpathTR) {
            $attrForSearchEmpty = array(
                array(
                    'attribute_code' => '',
                    'admin_title' => ''
                )
            );
            $this->_prepareDataForSearch($attrForSearchEmpty);
            $this->search($attrForSearchEmpty, 'attributes_grid');
            $this->assertTrue($this->checkCurrentPage('manage_attributes'), 'Wrong page is opened');
            $attrForDelete = array(
                array(
                    'attribute_code' => 'goext_test_attribute',
                    'admin_title' => 'general_dropdown_goext'
                )
            );
            $this->productAttributeHelper()->deleteAttributes($attrForDelete);
        }
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');

        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->addAttributeToSet(array('General' => 'goext_test_attribute'));
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        $this->logoutAdminUser();
    }
}
