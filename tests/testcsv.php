<?php declare(strict_types=1);

$curDir = __DIR__ .DIRECTORY_SEPARATOR. '.' .DIRECTORY_SEPARATOR;
require_once $curDir . 'Main.php';
require_once $curDir . 'LMFoundry_autoload.php';
use LM\Foundry\{Cast, Utils, Results};

function runCSV(string $dbFilename, string $moldFilename) : Results {
    
    $csvArray = CSVFileToArray($dbFilename);
    $curStore = '';
    $stores = [];
    foreach ($csvArray as $row) {
        $storeName = $row['store'];
        if ($curStore != $storeName) {
            if (isset($storeRecord)) {
                $stores[] = $storeRecord;
            }
            $storeRecord = [
                'store_name'   => $storeName, 
                'store_rating' => $row['store_rating'],
                'fruits'       => []
            ];
            $curStore = $row['store'];
        }
        $thisFruit = [
            'fruit_name' => $row['fruit_name'],
            'color' => $row['color'], 
            'price' => $row['price'] ];

        $storeRecord['fruits'][] = $thisFruit;
    }
    $numStores = count($stores);
    if (isset($storeRecord) && $numStores > 0  && $storeRecord != $stores[$numStores-1] )
    $stores[] = $storeRecord; // pick up the final store and put into list of stores

    $data = [
        "doc_title" => "Fruit Prices at Nearby Stores",
        "store_list" => $stores
    ];
    $results = Cast::pour($moldFilename, $data);
    return $results;
}

// Turn CSV File into an array of name=value arrays
function CSVFileToArray(string $csvFilename) : array {
    $handle = fopen($csvFilename, "r");
    $rowNum = 0;
    $array = array();
    $colNames = array();
    while (($data = fgetcsv($handle)) !== FALSE) {
        $rowNum++;
        if ($rowNum == 1) {
            $colNames = $data; // The first fetched will be the keys in the key=>value pairs of returned array
        } else {
            $thisRow = array();
            for ($i = 0; $i < count($colNames); $i++)
                if (isset($colNames[$i]) && isset($data[$i]))
                    $thisRow[$colNames[$i]] = $data[$i];
            $array[] = $thisRow;
        }
    }
    return $array;
}

echo Main::main($argc, $argv, 'csv', 'runCSV');
