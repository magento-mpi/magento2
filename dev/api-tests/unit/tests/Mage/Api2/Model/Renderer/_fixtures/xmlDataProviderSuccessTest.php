<?php
//case
$rootNode = Mage_Api2_Model_Renderer_Xml_Writer::XML_ROOT_NODE;

// case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <firstname>firstname</firstname>
  <lasttname>lasttname</lasttname>
  <address>address</address>
  <data_item_122>is_numeric</data_item_122>
  <data_item_1>is_numeric</data_item_1>
  <street>
    <data_item>street1</data_item>
    <data_item>street2</data_item>
  </street>
</{$rootNode}>

XML;
$data[] = array(
    'firstname' => 'firstname',
    'lasttname' => 'lasttname',
    'address' => 'address',
    '122' => 'is_numeric',
    ':1:' => 'is_numeric',
    'street' => array(
        0 => 'street1',
        1 => 'street2',
    )
);

// case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <firstname>firstname</firstname>
  <lasttname>lasttname</lasttname>
  <address>address</address>
  <data_item_122>is_numeric</data_item_122>
  <data_item_1>is_numeric</data_item_1>
  <street>
    <data_item_0>street1</data_item_0>
    <data_item_1>street2</data_item_1>
    <a>street3</a>
  </street>
</{$rootNode}>

XML;
$data[] = array(
    'firstname' => 'firstname',
    'lasttname' => 'lasttname',
    'address' => 'address',
    '122' => 'is_numeric',
    ':1:' => 'is_numeric',
    'street' => array(
        0 => 'street1',
        1 => 'street2',
        'a' => 'street3',
    )
);

// case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <data_item>test1</data_item>
  <data_item>test2</data_item>
  <data_item>
    <test01>some1</test01>
    <test02>some2</test02>
    <test03>
      <data_item_100test>some01</data_item_100test>
      <test002>some02</test002>
    </test03>
  </data_item>
</{$rootNode}>

XML;
$data[] = array(
    'test1',
    'test2',
    (object)array(
        'test01' => 'some1',
        'test02' => 'some2',
        'test03' => array(
            '100test' => 'some01',
            'test002' => 'some02',
        ),
    )
);

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <data_item_0>assoc_item1</data_item_0>
  <data_item_1>assoc_item2</data_item_1>
  <assoc_test001>&lt;some01&gt;text&lt;/some01&gt;</assoc_test001>
  <assoc.test002>1 &gt; 0</assoc.test002>
  <assoc_test003.>chars ]]&gt;</assoc_test003.>
  <assoc_test004>chars  !"#$%&amp;'()*+,/;&lt;=&gt;?@[\]^`{|}~  chars </assoc_test004>
  <key_chars__.>chars</key_chars__.>
</{$rootNode}>

XML;
$data[] = array(
    'assoc_item1',
    'assoc_item2',
    'assoc:test001' => '<some01>text</some01>',
    'assoc.test002' => '1 > 0',
    'assoc_test003.' => 'chars ]]>',
    'assoc_test004' => 'chars  !"#$%&\'()*+,/;<=>?@[\]^`{|}~  chars ',
    'key chars `\/;:][{}"|\'.,~!@#$%^&*()_+' => 'chars',
);

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <foo_bar></foo_bar>
</{$rootNode}>

XML;
$data[] = array('foo_bar' => '');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <data_item>some1</data_item>
</{$rootNode}>

XML;
$data[] = array('1' => 'some1');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <data_item_1.234>0.123</data_item_1.234>
</{$rootNode}>

XML;
$data[] = array('1.234' => .123);

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <foo>bar</foo>
</{$rootNode}>

XML;
$data[] = array('foo' => 'bar');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <data_item>string</data_item>
</{$rootNode}>

XML;
$data[] = 'string';

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}>
  <foo>&gt;bar=</foo>
</{$rootNode}>

XML;
$data[] = array('foo' => '>bar=');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}/>

XML;
$data[] = array();

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<{$rootNode}/>

XML;
$data[] = new stdClass();


$cnt = 0;
return array(
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt]),
);
