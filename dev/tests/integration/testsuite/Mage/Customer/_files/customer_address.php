<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$customerAddress = new Mage_Customer_Model_Address();
$customerAddress->setCustomerId(1)
    ->setData(array(
        'telephone' => 3468676,
        'postcode' => 75477,
        'country_id' => 'AL',
        'city' => '57u8iol',
        'street' => array('sdr6tyukj'),
        'lastname' => 'fyuikl',
        'firstname' => 'awefgaregv',
        'parent_id' => 1
    ));
$customerAddress->save();
