<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('imagequeue/compress'),
    'webp',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length'   => 1,
        'unsigned' => true,
        'nullable' => true,
        'default'  => 0,
        'comment'  => 'webp'
    )
);
$installer->getConnection()
        ->update($installer->getTable('imagequeue/compress'), array('webp' => 0));

$installer->endSetup();
