<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'field',
            \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL,
            \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
        )
    ),
    '$tableData' => array(
        array('field' => 'customer/customer'),
        array('field' => 'customer/attribute_data_postcode'),
        array('field' => 'customer/attribute_data_postcode::someMethod'),
        array('field' => '\Magento\Customer\Model\Customer'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'Magento_Customer_Model_Customer_FROM_MAP',
                'from'  => array('`field` = ?' => 'customer/customer')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '\Magento\Customer\Model\Attribute\Data\Postcode',
                'from'  => array('`field` = ?' => 'customer/attribute_data_postcode')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '\Magento\Customer\Model\Attribute\Data\Postcode::someMethod',
                'from'  => array('`field` = ?' => 'customer/attribute_data_postcode::someMethod')
            ),
        ),
        'aliases_map' => array(
            \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL => array(
                'customer/customer'                => 'Magento_Customer_Model_Customer_FROM_MAP',
                'customer/attribute_data_postcode' => '\Magento\Customer\Model\Attribute\Data\Postcode',
            ),
        )
    ),
    '$aliasesMap' => array(
        \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL => array(
            'customer/customer' => 'Magento_Customer_Model_Customer_FROM_MAP'
        )
    )
);
