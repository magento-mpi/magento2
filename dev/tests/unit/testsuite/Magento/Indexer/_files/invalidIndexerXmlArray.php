<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'without_indexer_handle' => array(
        '<?xml version="1.0"?><config></config>',
        array("Element 'config': Missing child element(s). Expected is ( indexer ).")),

    'indexer_with_notallowed_attribute' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name" notallowed="some value">'
        .'<title>Test</title><description>Test</description>'
        .'<depends><table name="same_entity" entity_column="entity_id" /></depends></indexer></config>',
        array("Element 'indexer', attribute 'notallowed': The attribute 'notallowed' is not allowed.")),

    'indexer_without_title' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">'
        .'<description>Test</description></indexer></config>',
        array("Element 'description': This element is not expected. Expected is ( title ).")),

    'indexer_without_description' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">'
        .'<title>Test</title><depends></depends></indexer></config>',
        array("Element 'depends': This element is not expected. Expected is ( description ).")),

    'indexer_without_depends' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">'
        .'<title>Test</title><description>Test</description></indexer></config>',
        array("Element 'indexer': Missing child element(s). Expected is ( depends ).")),

    'table_with_duplicate' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">'
        .'<title>Test</title><description>Test</description>'
        .'<depends><table name="some_entity" entity_column="entity_id" />'
        .'<table name="some_entity" entity_column="entity_id" /></depends></indexer></config>',
        array("Element 'table': Duplicate key-sequence ['some_entity', 'entity_id'] in unique"
            . " identity-constraint 'uniqueDependsTable'."
        )),

    'depends_without_table' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">'
        .'<title>Test</title><description>Test</description>'
        .'<depends></depends></indexer></config>',
        array("Element 'depends': Missing child element(s). Expected is ( table ).")),

    'table_without_name' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">'
        .'<title>Test</title><description>Test</description>'
        .'<depends><table entity_column="entity_id"/></depends></indexer></config>',
        array("Element 'table': The attribute 'name' is required but missing.")),

    'table_without_entity_column' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">'
        .'<title>Test</title><description>Test</description>'
        .'<depends><table name="some_entity" /></depends></indexer></config>',
        array("Element 'table': The attribute 'entity_column' is required but missing.")),
);