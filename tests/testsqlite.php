<?php declare(strict_types=1);

$curDir = __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR;
require_once $curDir . 'Main.php';
require_once $curDir . 'LMFoundry_autoload.php';
use LM\Foundry\{Cast, Utils, Results};

function runSqlite(string $dbFilename, string $moldFilename) : Results {
    // ====>>>> TO DO:
    // Open the sqlite db in the file
    // Do some queries. 
    // Make the queries ready for the 'pour' function
    // call cast->pour() method
    $results = new Results;
    $pdo = new PDO('sqlite:' . $dbFilename); // data\\stores.sqlite');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $sql = 'SELECT store_name, rating FROM store';
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $stores = $sth->fetchAll();
    $stores_data = [];

    foreach ($stores as $store) {
        $record = [
            'store_name'=>$store['store_name'],
            'store_rating'=>$store['rating'],
            'fruits' => []
        ];
        $sql = 'SELECT name, color, price FROM fruit where store_name = ?';
        $sth = $pdo->prepare($sql);
        $sth->bindValue(1, $store['store_name'], PDO::PARAM_STR);
        $sth->execute();
        $fruits = $sth->fetchAll();
        foreach ($fruits as $fruit) {
            $record['fruits'][] = [
                'fruit_name' => $fruit['name'],
                'color' => $fruit['color'], 
                'price' => $fruit['price']
            ];
        }
        $stores_data[] = $record;
    }
    $data = [
        "doc_title" => "Fruit Prices at Nearby Stores",
        "store_list" => $stores_data
    ];
    $results = Cast::pour($moldFilename, $data);
    return $results;
}

echo Main::main($argc, $argv, 'sqlite', 'runSqlite');
