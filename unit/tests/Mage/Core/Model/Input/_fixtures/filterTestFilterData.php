<?php
/**
 * Data which need to filter
 */
return array(
    'name1' => 'some <b>string</b>',
    'name2' => '888 555',
    'list_values' => array(
        'some <b>string2</b>',
        'some <p>string3</p>',
    ),
    'list_values_with_name' => array(
        'item1' => 'some <b onclick="alert(\'2\')">string4</b>',
        'item2' => 'some <b onclick="alert(\'1\')">string5</b>',
        'item3' => 'some <p>string5</p> <b>bold</b> <div>div</div>',
        'deep_list' => array(
            'sub1' => 'toLowString',
            'sub2' => '5 TO INT',
        )
    )
);
