<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'without_mview_handle' => array(
        '<?xml version="1.0"?><config></config>',
        array("Element 'config': Missing child element(s). Expected is ( view )."),
    ),

    'mview_with_notallowed_attribute' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><view id="view_one" notallow="notallow" class="Ogogo\Class\One">'
        . '<subscriptions><table name="some_entity" entity_column="entity_id" /></subscriptions></view></config>',
        array("Element 'view', attribute 'notallow': The attribute 'notallow' is not allowed."),
    ),

    'mview_without_class_attribute' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><view id="view_one" ><subscriptions>'
        . '<table name="some_entity" entity_column="entity_id" /></subscriptions></view></config>',
        array("Element 'view': The attribute 'class' is required but missing."),
    ),

    'mview_with_empty_subscriptions' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><view id="view_one" class="Ogogo\Class\One"><subscriptions>'
        . '</subscriptions></view></config>',
        array("Element 'subscriptions': Missing child element(s). Expected is ( table )."),
    ),

    'subscriptions_without_table' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><view id="view_one" class="Ogogo\Class\One"><subscriptions>'
        . '</subscriptions></view></config>',
        array("Element 'subscriptions': Missing child element(s). Expected is ( table )."),
    ),

    'table_without_column_attribute' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><view id="view_one" class="Ogogo\Class\One"><subscriptions>'
        . '<table name="some_entity" /></subscriptions></view></config>',
        array("Element 'table': The attribute 'entity_column' is required but missing."),
    ),

    'subscriptions_duplicate_table' => array(
        '<?xml version="1.0" encoding="UTF-8"?><config><view id="view_one" class="Ogogo\Class\One"><subscriptions>'
        . '<table name="some_entity" entity_column="entity_id" />'
        . '<table name="some_entity" entity_column="entity_id" /></subscriptions></view></config>',
        array("Element 'table': Duplicate key-sequence ['some_entity', 'entity_id'] in unique identity-constraint 'uniqueSubscriptionsTable'."),
    ),
);
