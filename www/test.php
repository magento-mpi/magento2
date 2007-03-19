<?php

$xml1 = simplexml_load_string("<root><child>sometext</child></root>");
$xml2 = simplexml_load_string("<child>another text</child>");
$xml1->child = $xml2;

echo "<xmp>"; print_r($xml1);

/*
try {
$dbh = new PDO('mysql:host=localhost;dbname=test', "root", "");
$res = $dbh -> query (
        'create table blah (id integer);
        insert into blah values(10);
        insert into blah values(20);
        update blah set id = 30 where id=10;
        delete from blah where id != 30;'
        );
var_dump($res);
} catch (Exception $e) {
    echo $e->getMessage();
}
*/
?>
