<?php
/**
 * Export model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export extends Mage_ImportExport_Model_Export
{
    /**
     * Return true if it is last task
     *
     * @return boolean
     */
    public function getIsLast()
    {
        return $this->_getEntityAdapter()->getIsLast();
    }

    /**
     * Retrieve export files destination dir
     *
     * @return string
     */
    protected function getDestinationDir()
    {
        return Mage::getBaseDir('media') . DS . 'importexport' . DS . 'export';
    }

    /**
     * Retrieve export file destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->getDestinationDir() . DS . $this->getEntity();
    }

    /**
     * Export data.
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function export()
    {
        try {
            $writer = $this->_getWriter();
            $page = $this->_getData('page');
            if ($page == 1) {
                $truncateResult = $writer->truncate();
                if ($truncateResult === false) {
                    $this->_getEntityAdapter()->setIsLast();
                    return $this;
                }
            }
            $this->addLogComment(Mage::helper('Mage_ImportExport_Helper_Data')
                ->__('Begin export page %s of %s', $page, $this->getEntity()));
            $this->_getEntityAdapter()
                ->setCurrentPage($page)
                ->setWriter($writer)
                ->export();
            if ($this->getIsLast()) {
                $writer->renameTemporaryFile();
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getEntityAdapter()->setIsLast();
            if ($writer) {
                //Stop export and try to remove temporary file if we have error
                $writer->truncate();
            }
        }
        unset($writer);
        return $this;
    }

    /**
     * Retrieve last export file information or false if not exists
     *
     * @return bool|Varien_Object
     */
    public function getLastExportInfo()
    {
        $exportFiles = glob('{' . $this->getDestinationDir() .'/*.csv' . '}', GLOB_BRACE);
        if (!count($exportFiles)) {
            return false;
        }
        foreach ($exportFiles as &$exportFile) {
            $path = $exportFile;
            $timestamp = filemtime($path);
            $dateSuffix   = date('Ymd_His', $timestamp);
            $downloadName = preg_replace('/^(.+)(\.[^.]+)$/', '\1_' . $dateSuffix . '\2', basename($path));
            $exportFile = new Varien_Object(array(
                'path' => $path,
                'download_name' => $downloadName,
                'size' => filesize($path),
                'timestamp' => $timestamp,
            ));
        }
        $lastFile = reset($exportFiles);
        return $lastFile;
    }

    /**
     * Remove last export file
     *
     * @return Saas_ImportExport_Model_Export
     */
    public function removeLastExportFile()
    {
        $exportFiles = glob('{' . $this->getDestinationDir() . '/*}', GLOB_BRACE);
        foreach ($exportFiles as $exportFile) {
            if (!unlink($exportFile)) {
                Mage::throwException(Mage::helper('Saas_ImportExport_Helper_Data')->__('File has not been removed'));
            }
        }
        return $this;
    }

    /**
     * Get writer object.
     *
     * @throws Mage_Core_Exception
     * @return Saas_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _getWriter()
    {
        if (!$this->_writer) {
            $validWriters = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_FORMATS);

            if (isset($validWriters[$this->getFileFormat()])) {
                try {
                    $arguments = array('destination' => $this->getDestination());
                    $this->_writer = Mage::getModel($validWriters[$this->getFileFormat()]['model'], $arguments);

                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('Saas_ImportExport_Helper_Data')->__('Invalid entity model')
                    );
                }
            } else {
                Mage::throwException(Mage::helper('Saas_ImportExport_Helper_Data')->__('Invalid file format'));
            }
        }
        return $this->_writer;
    }
}
