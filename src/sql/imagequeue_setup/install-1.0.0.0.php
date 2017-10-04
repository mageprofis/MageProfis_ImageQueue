<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('imagequeue/compress'))
    ->addColumn('compress_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'length' => 65536
        ), 'Title')
    ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'length' => 1,
        'default' => 0
        ), 'Title')
    ->addColumn('suffix', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'length' => 8,
        'default' => ''
        ), 'Title')
    ->setComment('ImageQueue Compress Database');
$installer->getConnection()->createTable($table);

$installer->endSetup();