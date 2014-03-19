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
        array("Element 'config': Missing child element(s). Expected is ( indexer ).")
    ),
    'indexer_with_notallowed_attribute' => array(
        '<?xml version="1.0"?><config>' .
        '<indexer id="somename" view_id="view_01" class="Class\Name" notallowed="some value">' .
        '<title>Test</title><description>Test</description></indexer></config>',
        array("Element 'indexer', attribute 'notallowed': The attribute 'notallowed' is not allowed.")
    ),
    'indexer_without_view_attribute' => array(
        '<?xml version="1.0"?><config><indexer id="somename" class="Class\Name">' .
        '<title>Test</title><description>Test</description></indexer></config>',
        array("Element 'indexer': The attribute 'view_id' is required but missing.")
    ),
    'indexer_duplicate_view_attribute' => array(
        '<?xml version="1.0"?><config><indexer id="somename" view_id="view_01" class="Class\Name">' .
        '<title>Test</title><description>Test</description></indexer>' .
        '<indexer id="somename_two" view_id="view_01" class="Class\Name">' .
        '<title>Test</title><description>Test</description></indexer></config>',
        array("Element 'indexer': Duplicate key-sequence ['view_01'] in unique identity-constraint 'uniqueViewId'.")
    ),
    'indexer_without_title' => array(
        '<?xml version="1.0"?><config><indexer id="somename" view_id="view_01" class="Class\Name">' .
        '<description>Test</description></indexer></config>',
        array("Element 'description': This element is not expected. Expected is ( title ).")
    ),
    'indexer_without_description' => array(
        '<?xml version="1.0"?><config><indexer id="somename" view_id="view_01" class="Class\Name">' .
        '<title>Test</title></indexer></config>',
        array("Element 'indexer': Missing child element(s). Expected is ( description ).")
    )
);
