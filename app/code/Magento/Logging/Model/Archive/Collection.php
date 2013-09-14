<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive files collection
 */
class Magento_Logging_Model_Archive_Collection extends Magento_Data_Collection_Filesystem
{
    /**
     * Filenames regex filter
     *
     * @var string
     */
    protected $_allowedFilesMask = '/^[a-z0-9\.\-\_]+\.csv$/i';

    /**
     * Locale model
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Set target dir for scanning
     *
     * @param Magento_Logging_Model_Archive $archive
     * @param Magento_Core_Model_LocaleInterface $locale
     */
    public function __construct(
        Magento_Logging_Model_Archive $archive,
        Magento_Core_Model_LocaleInterface $locale
    ) {
        parent::__construct();
        $basePath = $archive->getBasePath();
        $file = new Magento_Io_File();
        $file->setAllowCreateFolders(true)->createDestinationDir($basePath);
        $this->addTargetDir($basePath);
        $this->_locale = $locale;
    }

    /**
     * Row generator
     * Add 'time' column as Zend_Date object
     * Add 'timestamp' column as unix timestamp - used in date filter
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        $date = new Zend_Date(str_replace('.csv', '', $row['basename']), 'yyyyMMddHH', $this->_locale->getLocaleCode());
        $row['time'] = $date;
        /**
         * Used in date filter, becouse $date contains hours
         */
        $dateWithoutHours = new Zend_Date(str_replace('.csv', '', $row['basename']), 'yyyyMMdd',
            $this->_locale->getLocaleCode());
        $row['timestamp'] = $dateWithoutHours->toString('yyyy-MM-dd');
        return $row;
    }

    /**
     * Custom callback method for 'moreq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsMoreThan($field, $filterValue, $row)
    {
        $rowValue = $row[$field];
        if ($field == 'time') {
            $rowValue    = $row['timestamp'];
        }
        return $rowValue > $filterValue;
    }

    /**
     * Custom callback method for 'lteq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsLessThan($field, $filterValue, $row)
    {
        $rowValue = $row[$field];
        if ($field == 'time') {
            $rowValue    = $row['timestamp'];
        }
        return $rowValue < $filterValue;
    }
}
