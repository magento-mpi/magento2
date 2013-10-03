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
            'comment' => 'File Content',
        )
    );
    $queryString = "
        DECLARE
          v_clob Clob;
          v_blob Blob;
          v_in Pls_Integer := 1;
          v_out Pls_Integer := 1;
          v_lang Pls_Integer := 0;
          v_warning Pls_Integer := 0;
        BEGIN
          FOR row IN (SELECT file_id, {$originColumnName} from {$fileStorageTable})
          LOOP
            if row.{$originColumnName} is null then v_blob:=null;
            else
              v_clob:=row.{$originColumnName};
              v_in:=1;
              v_out:=1;
              dbms_lob.createtemporary(v_blob,TRUE);
              dbms_lob.convertToBlob(
                v_blob,
                v_clob,
                DBMS_lob.getlength(v_clob),
                v_in,
                v_out,
                DBMS_LOB.default_csid,
                v_lang,
                v_warning
              );
            end if;
            update {$fileStorageTable} set {$temporaryColumnName}=v_blob where file_id=row.file_id;
          END LOOP;
          commit;
        END;";
    $connection->query(trim($queryString));
    $connection->dropColumn($fileStorageTable, $originColumnName);
    $connection->query("ALTER TABLE {$fileStorageTable} RENAME COLUMN {$temporaryColumnName} TO {$originColumnName}");
}
