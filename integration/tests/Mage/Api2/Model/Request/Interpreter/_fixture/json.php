<?php
return array(
    'decoded' => array(
        'test1',
        'test2',
        array(
            'some0',
            'test01' => 'some1',
            'test02' => 'some2',
            'some3'
        )
    ),
    'encoded' => '["test1","test2",{"0":"some0","test01":"some1","test02":"some2","1":"some3"}]',
    'invalid_encoded' => '"test1","test2",{"0":"some0","test01":"some1","test02":"some2","1":"some3"]'
);
