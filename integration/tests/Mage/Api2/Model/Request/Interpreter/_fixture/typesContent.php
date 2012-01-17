<?php
$xml = <<<HTML
<?xml version="1.0"?>
<xml>
    <key1>test1</key1>
    <key2>test2</key2>
    <array>
        <test01>some1</test01>
        <test02>some2</test02>
    </array>
</xml>
HTML;

return array(
    'decoded' => array(
        'key1' => 'test1',
        'key2' => 'test2',
        'array' => array(
            'test01' => 'some1',
            'test02' => 'some2',
        )
    ),
    'json_encoded'          => '{"key1":"test1","key2":"test2","array":{"test01":"some1","test02":"some2"}}',
    'json_invalid_encoded'  => '"test1","test2",{"0":"some0","test01":"some1","test02":"some2","1":"some3"]',
    'xml_encoded'           => $xml,
    'xml_invalid_encoded'   => '<xml </array </xml>',
    'query_encoded'         => 'key1=test1&key2=test2&array[test01]=some1&array[test02]=some2',
    'query_invalid_encoded' => 'key1=test1key2=test2&array[test01=some1&array[test02]=some2'
);
