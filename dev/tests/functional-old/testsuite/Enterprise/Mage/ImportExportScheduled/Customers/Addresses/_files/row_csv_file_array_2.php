<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $this Mage_Selenium_TestCase */
 return array(
     $this->loadDataSet('ImportExport', 'generic_address_csv', array(
             '_entity_id' => '',
             '_email' => '<realEmail>',
             'city' => 'Harrisburg',
             'company' => 'The Flying Bear',
             'fax' => '717-503-8908',
             'firstname' => 'Ronald',
             'lastname' => 'Armstrong',
             'middlename' => 'M.',
             'postcode' => '17111',
             'region' => 'Pennsylvania',
             'street' => '154 Saint James Drive',
             'telephone' => '717-503-8908',
         )
     ),
     $this->loadDataSet('ImportExport', 'generic_address_csv', array(
             '_entity_id' => '',
             '_email' => '<realEmail>',
             'city' => 'Cambridge',
             'company' => 'Best Biz Survis',
             'fax' => '781-210-5960',
             'firstname' => 'Arvilla',
             'lastname' => 'Hubbs',
             'middlename' => 'P.',
             'postcode' => '02142',
             'region' => 'Massachusetts',
             'street' => '3862 Wescam Court',
             'telephone' => '781-210-5960',
         )
     ),
 );