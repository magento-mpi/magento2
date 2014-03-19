<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$connection = $installer->getConnection();

$fileStorageTable = $installer->getTable('core_file_storage');
if ($installer->tableExists($fileStorageTable)) {
    $temporaryColumnName = 'newcontent';
    $originColumnName = 'content';
    $connection->addColumn(
        $fileStorageTable,
        $temporaryColumnName,
        array(
            'type' => \Magento\DB\Ddl\Table::TYPE_VARBINARY,
            'size' => \Magento\DB\Ddl\Table::MAX_VARBINARY_SIZE,
            'nullable' => true,
            'comment' => 'File Content'
        )
    );
    $queryString = "\n        DECLARE\n          v_clob Clob;\n          v_blob Blob;\n          v_in Pls_Integer := 1;\n          v_out Pls_Integer := 1;\n          v_lang Pls_Integer := 0;\n          v_warning Pls_Integer := 0;\n        BEGIN\n          FOR row IN (SELECT file_id, {$originColumnName} from {$fileStorageTable})\n          LOOP\n            if row.{$originColumnName} is null then v_blob:=null;\n            else\n              v_clob:=row.{$originColumnName};\n              v_in:=1;\n              v_out:=1;\n              dbms_lob.createtemporary(v_blob,TRUE);\n              dbms_lob.convertToBlob(\n                v_blob,\n                v_clob,\n                DBMS_lob.getlength(v_clob),\n                v_in,\n                v_out,\n                DBMS_LOB.default_csid,\n                v_lang,\n                v_warning\n              );\n            end if;\n            update {$fileStorageTable} set {$temporaryColumnName}=v_blob where file_id=row.file_id;\n          END LOOP;\n          commit;\n        END;";
    $connection->query(trim($queryString));
    $connection->dropColumn($fileStorageTable, $originColumnName);
    $connection->query("ALTER TABLE {$fileStorageTable} RENAME COLUMN {$temporaryColumnName} TO {$originColumnName}");
}
