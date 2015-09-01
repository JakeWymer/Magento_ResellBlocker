<?php

$this->startSetup();


$table = new Varien_Db_Ddl_Table();


$table->setName($this->getTable('jakey_resellblocker/basetime'));



$table->addColumn(
    'id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    10,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);

$table->addColumn(
    'base_time',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    null,
    array(
        'nullable' => false,
    )
);

$table->addColumn(
    'time_frame',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    10,
    array(
        'nullable' => false,
    )
);

$table->setOption('type', 'InnoDB');
$table->setOption('charset', 'utf8');


$this->getConnection()->createTable($table);

//TABLE 2

$table2 = new Varien_Db_Ddl_Table();

$table2->setName($this->getTable('jakey_resellblocker/emaillist'));

$table2->addColumn(
    'id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    10,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);

$table2->addColumn(
    'customer_email',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    30,
    array(
        'nullable' => false,
    )
);

$table2->addColumn(
    'email_date',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    null,
    array(
        'nullable' => false,
    )
);

$table2->addColumn(
    'order_number',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    30,
    array(
        'nullable' => false,
    )
);


$table2->setOption('type', 'InnoDB');
$table2->setOption('charset', 'utf8');


$this->getConnection()->createTable($table2);

$this->endSetup();

?>