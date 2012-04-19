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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'customer.php';
$customer = Magento_Test_Webservice::getFixture('creditmemo/customer');

$customerAddress = new Mage_Customer_Model_Address();
$customerAddress->setData(array(
    'city'                => 'New York',
    'country_id'          => 'US',
    'fax'                 => '56-987-987',
    'firstname'           => 'Jacklin',
    'lastname'            => 'Sparrow',
    'middlename'          => 'John',
    'postcode'            => '10012',
    'region'              => 'New York',
    'region_id'           => '43',
    'street'              => 'Main Street',
    'telephone'           => '718-452-9207',
    'is_default_billing'  => true,
    'is_default_shipping' => true
));
$customerAddress->setCustomer($customer);
$customerAddress->save();
Magento_Test_Webservice::setFixture('creditmemo/customer_address', $customerAddress);
