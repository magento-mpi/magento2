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
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
            Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED
        )
    ),
    '$tableData' => array(
        array('field' => 'a:1:{s:5:"model";s:34:"catalogrule/rule_condition_combine";}'),
        array('field' => 'a:1:{s:5:"model";s:16:"some random text";}'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => 'a:1:{s:5:"model";s:48:"Magento_CatalogRule_Model_Rule_Condition_Combine";}',
                'from'  => array('`field` = ?' => 'a:1:{s:5:"model";s:34:"catalogrule/rule_condition_combine";}')
            ),
        ),
        'aliases_map' => array(
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
                'catalogrule/rule_condition_combine' => 'Magento_CatalogRule_Model_Rule_Condition_Combine',
            )
        )
    ),
);
