<?php
$pdo = new PDO('sqlite:stores.sqlite');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

function create_table($db, string $table, array $columns, array $types ) {
    $sql = 'create table ' . $table . '(';
    for ($i=0; $i < count($columns); $i++) {
        if (isset($types[$i])) {
            if ($i > 0) {
                $sql .= ', ';
            }
            $sql .= $columns[$i] . ' ' . $types[$i];
        } else {
            echo "Missing type for column '" . $columns[$i] . "'\n";
        }
    }
    // E.G. $db->exec('create table fruit (name varchar2, color varchar2, price varchar2)');
    $sql .= ')';
    echo "SQL: $sql\n";
    $db->exec($sql);
}

function drop_table($db, string $table) {
    $sql = 'drop table if exists ' . $table;
    echo "SQL: $sql\n";
    $db->exec($sql);
}

function insert_row($db, $table, $values, $quotes){
    $sql = 'insert into ' . $table . ' values (';
    for ($i=0; $i < count($values); $i++) {
        if ($i > 0) {
            $sql .= ', ';
        }
        if ($quotes[$i]) $sql .= "'";
        $sql .= strval($values[$i]);
        if ($quotes[$i]) $sql .= "'";
    }
    $sql .= ')';
    echo $sql . "\n";
    $db->exec($sql);
}

function select_all($db, $table) {
    $sql = 'select * from ' . $table;
    echo $sql."\n";
    $sth = $db->prepare($sql);
    $sth->execute();
    return $sth->fetchAll();
}

drop_table($pdo, 'fruit');
drop_table($pdo, 'store');

create_table($pdo, 'store', ['store_name', 'rating'], ['varchar2', 'real' ]);

insert_row($pdo, 'store', ['Green Grocer', 3.6], [true, false]);
insert_row($pdo, 'store', ['4th St Food Co-op', 4.6], [true, false]);
print_r(select_all($pdo, 'store')); echo "\n";

create_table($pdo, 'fruit', ['store_name', 'name', 'color', 'price' ], ['varchar2', 'varchar2', 'varchar2', 'real']);

$stores = [ ['Green Grocer', [
    ['Red Delicious Apple','red', 0.79],
    ['Golden Delicious Apple', 'yellow', 0.85 ],
    ['Gravenstein Apple', 'yellow', 0.89],
    ['Fuji Apple', 'red', 0.79]
]  ],
[ '4th St Food Co-op', [
    [ "Honeycrisp Apple",     "red",          1.09 ],
    [ "Granny Smith Apple",   "green",        0.79 ],
    [ "Pink Lady Apple",      "red & green",  0.79 ],
    [ "Gala Apple",           "red & yellow", 0.59 ],
    [ "Braeburn Apple",       "orange",       0.99 ],
    [ "Mcintosh Apple",       "red",          0.69 ]
] ] 
];
foreach ($stores as $store) {
    $fruit = $store[1];
    for ($i=0; $i < count($fruit); $i++) {
        $f = $fruit[$i];
        insert_row($pdo, 'fruit', 
            [ $store[0], $f[0], $f[1], $f[2] ], 
            [ true, true, true, false] );
    }
}

print_r(select_all($pdo, 'fruit')); echo "\n";
