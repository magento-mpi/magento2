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
                    $arguments = array(
                        'path' => $this->getDestination()
                    );
                    $this->_writer = Mage::getModel($validWriters[$this->getFileFormat()]['model'], $arguments);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('Mage_ImportExport_Helper_Data')->__('Invalid entity model')
                    );
                }
                if (! $this->_writer instanceof Saas_ImportExport_Model_Export_Adapter_Abstract) {
                    Mage::throwException(
                        Mage::helper('Saas_ImportExport_Helper_Data')
                            ->__('Adapter object must be an instance of %s',
                                'Saas_ImportExport_Model_Export_Adapter_Abstract'
                            )
                    );
                }
            } else {
                Mage::throwException(Mage::helper('Saas_ImportExport_Helper_Data')->__('Invalid file format'));
            }
        }
        return $this->_writer;
    }
}
