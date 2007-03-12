<?php
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
?>
