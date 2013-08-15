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
            Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
        )
    ),
    '$tableData' => array(
        array('field' => '<reference><block type="catalog/product_new" /></reference>'),
        array('field' => '<reference><block type="catalogSearch/result" /></reference>'),
        array('field' => '<reference></reference>'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<reference><block type="Magento_Catalog_Block_Product_New" /></reference>',
                'from'  => array('`field` = ?' => '<reference><block type="catalog/product_new" /></reference>')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<reference><block type="Magento_CatalogSearch_Block_Result" /></reference>',
                'from'  => array('`field` = ?' => '<reference><block type="catalogSearch/result" /></reference>')
            ),
        ),
        'aliases_map' => array(
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK => array(
                'catalog/product_new'  => 'Magento_Catalog_Block_Product_New',
                'catalogSearch/result' => 'Magento_CatalogSearch_Block_Result',
            )
        )
    ),
);
