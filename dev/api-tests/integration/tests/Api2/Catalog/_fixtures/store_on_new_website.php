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
if (!Magento_Test_Webservice::getFixture('store_on_new_website')) {
    $website = new Mage_Core_Model_Website();
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website',
            'default_group_id' => 1,
        )
    );
    $website->save();
    Magento_Test_Webservice::setFixture('website', $store);

    $store = new Mage_Core_Model_Store();
    $store->setData(array(
        'group_id' => $website->getDefaultGroupId(),
        'name' => 'Test Store View',
        'code' => 'store_' . uniqid(),
        'is_active' => true,
        'website_id' => $website->getId()
    ))->save();
    Mage::app()->reinitStores();
    Magento_Test_Webservice::setFixture('store_on_new_website', $store);
}
