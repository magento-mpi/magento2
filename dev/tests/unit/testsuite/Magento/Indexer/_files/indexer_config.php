<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'inputXML' => '<?xml version="1.0" encoding="UTF-8"?><config>'
        . '<indexer id="indexer_internal_name" view_id="view01" class="Index\Class\Name" group="some_group">'
        . '<title translate="true">'
        . 'Indexer public name</title><description translate="true">Indexer public description</description>'
        . '</indexer></config>',
    'expected' => array(
        'indexer_internal_name' => array(
            'indexer_id' => 'indexer_internal_name',
            'view_id' => 'view01',
            'action_class' => 'Index\Class\Name',
            'group' => 'some_group',
            'title' => 'Indexer public name',
            'description' => 'Indexer public description',
        ),
    ),
);
