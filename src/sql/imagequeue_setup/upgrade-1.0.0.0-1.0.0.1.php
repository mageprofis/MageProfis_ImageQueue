<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('imagequeue/compress'),
    'filename_hash',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'   => 255,
        'unsigned' => true,
        'nullable' => true,
        'default'  => '',
        'comment'  => 'Filename sha256 hash'
    )
);

$query = $installer->getConnection()
    ->select()
    ->from($installer->getTable('imagequeue/compress'), array('compress_id', 'filename'))
    ;
foreach ($installer->getConnection()->fetchAll($query) as $_row)
{
    $sha256 = hash('sha256', $_row['filename']);
    $query = $installer->getConnection()
        ->update($installer->getTable('imagequeue/compress'), array('filename_hash' => $sha256), 'compress_id = '.$_row['compress_id']);
    $where = $installer->getConnection()->quoteInto('filename_hash = ?', $sha256)
             . ' AND '
             . $installer->getConnection()->quoteInto('compress_id != ?', $_row['compress_id']);
    $installer->getConnection()->delete($installer->getTable('imagequeue/compress'), $where);
}

$installer->getConnection()
    ->addIndex(
        $installer->getTable('imagequeue/compress'),
        $installer->getIdxName('imagequeue/compress', 'filename_hash'),
        'filename_hash',
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

$installer->endSetup();
