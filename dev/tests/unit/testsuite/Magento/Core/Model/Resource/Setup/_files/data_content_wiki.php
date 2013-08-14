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
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
            Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI
        )
    ),
    '$tableData' => array(
        array('field' => '<p>{{widget type="productalert/product_view"}}</p>'),
        array('field' => '<p>{{widget type="catalogSearch/result"}}</p>'),
        array('field' => '<p>Some HTML code</p>'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<p>{{widget type="Magento_ProductAlert_Block_Product_View"}}</p>',
                'from'  => array('`field` = ?' => '<p>{{widget type="productalert/product_view"}}</p>')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<p>{{widget type="Magento_CatalogSearch_Block_Result"}}</p>',
                'from'  => array('`field` = ?' => '<p>{{widget type="catalogSearch/result"}}</p>')
            ),
        ),
        'aliases_map' => array(
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK => array(
                'productalert/product_view' => 'Magento_ProductAlert_Block_Product_View',
                'catalogSearch/result'      => 'Magento_CatalogSearch_Block_Result',
            )
        )
    ),
);
