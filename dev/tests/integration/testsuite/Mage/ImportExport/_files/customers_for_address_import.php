<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
//Create customer
$customer = new Mage_Customer_Model_Customer();
$customer
    ->setWebsiteId(0)
    ->setEntityId(1)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('BetsyParker@example.com')
    ->setPassword('password')
    ->setGroupId(0)
    ->setStoreId(0)
    ->setIsActive(1)
    ->setFirstname('Betsy')
    ->setLastname('Parker')
    ->setGender(2);
$customer->isObjectNew(true);
$customer->save();

// Create and set addresses
$addressFirst = new Mage_Customer_Model_Address();
$addressFirst->addData(array(
    'entity_id'         => 1,
    'firstname'         => 'Betsy',
    'lastname'          => 'Parker',
    'street'            => '1079 Rocky Road',
    'city'              => 'Philadelphia',
    'country_id'        => 'US',
    'region_id'         => '51',
    'postcode'          => '19107',
    'telephone'         => '215-629-9720',
));
$addressFirst->isObjectNew(true);
$customer->addAddress($addressFirst);
$customer->setDefaultBilling($addressFirst->getId());

$addressSecond = new Mage_Customer_Model_Address();
$addressSecond->addData(array(
    'entity_id'         => 2,
    'firstname'         => 'Anthony',
    'lastname'          => 'Nealy',
    'street'            => '3176 Cambridge Court',
    'city'              => 'Fayetteville',
    'country_id'        => 'US',
    'region_id'         => '5',
    'postcode'          => '72701',
    'telephone'         => '479-899-9849',
));
$addressSecond->isObjectNew(true);
$customer->addAddress($addressSecond);
$customer->setDefaultShipping($addressSecond->getId());
$customer->isObjectNew(true);
$customer->save();

$fixtureKey = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Customer';
Mage::unregister($fixtureKey);
Mage::register($fixtureKey, $customer);

// important data from address_import_update.csv (postcode is key)
$csvData = array(
    'address' => array( // address records
        'update'            => '19107',  // address with updates
        'new'               => '85034',  // new address
        'no_customer'       => '33602',  // there is no customer with this primary key (email+website)
        'new_no_address_id' => '32301',  // new address without address id
    ),
    'update'  => array( // this data is changed in CSV file
        '19107' => array(
            'firstname'  => 'Katy',
            'middlename' => 'T.',
        ),
    ),
    'remove'  => array( // this data is not set in CSV file
        '19107' => array(
            'city'   => 'Philadelphia',
            'region' => 'Pennsylvania',
        ),
    ),
    'default' => array( // new default billing/shipping addresses
        'billing'  => '85034',
        'shipping' => '19107',
    ),
);

$fixtureKey = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Csv_Update';
Mage::unregister($fixtureKey);
Mage::register($fixtureKey, $csvData);

// important data from address_import_delete.csv (postcode is key)
$csvData = array(
    'delete'     => '19107',  // deleted address
    'not_delete' => '72701',  // not deleted address
);

$fixtureKey = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Csv_Delete';
Mage::unregister($fixtureKey);
Mage::register($fixtureKey, $csvData);
