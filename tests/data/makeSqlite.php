<?php

function make_table($db) {
    $db->exec('create table fruit (name varchar2, color varchar2, price varchar2)');
    $stmt_h = $db->prepare('insert into fruit values (:val_1, :val_2, :val_3)');
  
    $v1 = ''; $v2=''; $v3='';
    $stmt_h->bindParam(':val_1', $v1);
    $stmt_h->bindParam(':val_2', $v2);
    $stmt_h->bindParam(':val_3', $v3);

    $v1 = 'pear';
    $v2 = 'green';
    $v3 = '.79';
    $stmt_h->execute();

    $v1 = 'mango';
    $v2 = 'orange';
    $v3 = '1.50';
    $stmt_h->execute();

    $v1 = 'watermelon';
    $v2 = 'green';
    $v3 = '2.50';
    $stmt_h->execute();

    $v1 = 'nectarine';
    $v2 = 'red';
    $v3 = '0.55';
    $stmt_h->execute();
}

function select($dbh) {
    $sth = $dbh->prepare("SELECT name, color, price FROM fruit");
    $sth->execute();
    return $sth->fetchAll();
}

/*$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$dbfilename = 'data'. DIRECTORY_SEPARATOR . 'fruit.sqlite';
$dsn = 'sqlite:' . $dbfilename;*/
try {
    $db = new PDO('sqlite:data' . DIRECTORY_SEPARATOR . 'fruit.sqlite');
    if ($db) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_TIMEOUT, 15);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //make_table($db);
    print_r(select($db));
    }
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
