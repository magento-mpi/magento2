<?php

$xml = simplexml_load_string("<root><child><grand>test1</grand></child><child><grand>test2</grand></child></root>");
$grand = $xml->child->grand;

echo "<xmp>"; 
print_r($xml);
print_r($grand);
print_r($grand->xpath('..'));

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
