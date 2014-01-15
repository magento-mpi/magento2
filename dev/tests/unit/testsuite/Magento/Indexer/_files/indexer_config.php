<?php
return array(
    'inputXML' => '<?xml version="1.0" encoding="UTF-8"?><config>'
        . '<indexer id="indexer_internal_name" view_id="view01" class="Index\Class\Name"><title translate="true">'
        . 'Indexer public name</title><description translate="true">Indexer public description</description>'
        . '</indexer></config>',
    'expected' => array(
        'indexer_internal_name' => array(
            'id' => 'indexer_internal_name',
            'view_id' => 'view01',
            'class' => 'Index\Class\Name',
            'title' => 'Indexer public name',
            'title_translate' => 'true',
            'description' => 'Indexer public description',
            'description_translate' => 'true',
        ),
    ),
);