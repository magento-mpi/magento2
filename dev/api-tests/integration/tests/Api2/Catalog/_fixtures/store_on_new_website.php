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
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
if (!Magento_Test_Webservice::getFixture('website')) {
    $website = new Mage_Core_Model_Website();
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website',
            'default_group_id' => 1,
        )
    );
    $website->save();
    Magento_Test_Webservice::setFixture('website', $website);
}

if (!Magento_Test_Webservice::getFixture('store_group')) {
    $defaultCategoryId = 2;
    $storeGroup = new Mage_Core_Model_Store_Group();
    $storeGroup->setData(array(
        'website_id' => Magento_Test_Webservice::getFixture('website')->getId(),
        'name' => 'Test Store' . uniqid(),
        'code' => 'store_group_' . uniqid(),
        'root_category_id' => $defaultCategoryId
    ))->save();
    Magento_Test_Webservice::setFixture('store_group', $storeGroup);
}

if (!Magento_Test_Webservice::getFixture('store_on_new_website')) {
    $store = new Mage_Core_Model_Store();
    $store->setData(array(
        'group_id' => Magento_Test_Webservice::getFixture('store_group')->getId(),
        'name' => 'Test Store View',
        'code' => 'store_' . uniqid(),
        'is_active' => true,
        'website_id' => Magento_Test_Webservice::getFixture('website')->getId()
    ))->save();
    Magento_Test_Webservice::setFixture('store_on_new_website', $store);
    Mage::app()->reinitStores();
}
