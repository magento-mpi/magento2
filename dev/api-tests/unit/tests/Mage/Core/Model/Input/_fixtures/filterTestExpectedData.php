<?php
/**
 * Expected data after testing filtering
 */
return array(
    'name1' => 'SOME STRING',
    'name2' => '888555',
    'list_values' => array(
        0 => 'SOME STRING2',
        1 => 'SOME STRING3',
    ),
    'list_values_with_name' => array(
        'item1' => 'SOME <B ONCLICK="ALERT(\'2\')">STRING4</B>',
        'item2' => 'some <b >string5</b>',
        'item3' => 'some &lt;p&gt;string5&lt;/p&gt; bold &lt;div&gt;div&lt;/div&gt;',
        'deep_list' => array(
            'sub1' => 'tolowstring',
            'sub2' => 5,
        )
    ),
);
