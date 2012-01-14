<?php
/**
 * {license_notice}
 *
 * @category     Mage
 * @package      Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to work with database backups
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Db extends Mage_Backup_Abstract
{
    /**
     * Implementation Rollback functionality for Db
     *
     * @return bool
     */
    public function rollback()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $this->_lastOperationSucceed = false;

        $archiveManager = new Mage_Archive();
        $source = $archiveManager->unpack($this->getBackupPath(), $this->getBackupsDir());

        $this->getResourceModel()->beginTransaction();

        $file = fopen($source, "r");
        $query = '';
        while(!feof($file)) {
            $line = fgets($file);
            $query .= $line;
            if ($this->_isLineLastInCommand($line)){
                $this->getResourceModel()->runCommand($query);
                $query = '';
            }
        }
        fclose($file);
        $this->getResourceModel()->commitTransaction();
        @unlink($source);

        $this->_lastOperationSucceed = true;

        return true;
    }

    /**
     * Check is line a last in sql command
     *
     * @param $line
     * @return bool
     */
    protected function _isLineLastInCommand($line)
    {
        $cleanLine = trim($line);
        $lineLength = strlen($cleanLine);

        $returnResult = false;
        if ($lineLength > 0){
            $lastSymbolIndex = $lineLength-1;
            if ($cleanLine[$lastSymbolIndex] == ';'){
                $returnResult = true;
            }
        }

        return $returnResult;
    }

    /**
     * Implementation Create Backup functionality for Db
     *
     * @return boolean
     */
    public function create()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $this->_lastOperationSucceed = false;

        $backup = Mage::getModel('Mage_Backup_Model_Backup')
            ->setTime($this->getTime())
            ->setType($this->getType())
            ->setPath($this->getBackupsDir())
            ->setName($this->getName());

        $backupDb = Mage::getModel('Mage_Backup_Model_Db');
        $backupDb->createBackup($backup);

        $this->_lastOperationSucceed = true;

        return true;
    }

    /**
     * Get Backup Type
     *
     * @return string
     */
    public function getType()
    {
        return "db";
    }
}
