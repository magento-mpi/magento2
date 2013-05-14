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
            '_action' => 'delete',
            'city' => 'Chattanooga',
            'company' => 'Hit or Miss',
            'fax' => '423-313-8300',
            'firstname' => 'Maureen',
            'lastname' => 'Velez',
            'middlename' => 'M.',
            'postcode' => '17111',
            'region' => 'Pennsylvania',
            'street' => '154 Saint James Drive',
            'telephone' => '423-313-8300',
        )
    ),
    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_entity_id' => '',
            '_email' => '<realEmail>',
            '_action' => 'update',
            'city' => 'Stockbridge',
            'company' => 'White Tower Hamburgers',
            'fax' => '678-565-2507',
            'firstname' => 'Lisa',
            'lastname' => 'Lewis',
            'middlename' => 'M.',
            'postcode' => '30281',
            'region' => 'Georgia',
            'street' => '3292 Hanifan Lane',
            'telephone' => '678-565-2507',
        )
    ),
    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_entity_id' => '',
            '_email' => '<realEmail>',
            '_action' => '',
            'city' => 'San Diego',
            'company' => 'Security Sporting Goods',
            'fax' => '619-696-3735',
            'firstname' => 'Luis',
            'lastname' => 'Meade',
            'middlename' => 'J.',
            'postcode' => '92101',
            'region' => 'California',
            'street' => '1776 Grim Avenue',
            'telephone' => '619-696-3735',
        )
    ),
);