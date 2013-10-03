<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive files collection
 */
namespace Magento\Logging\Model\Archive;

class Collection extends \Magento\Data\Collection\Filesystem
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
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * Set target dir for scanning
     *
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logging\Model\Archive $archive
     * @param \Magento\Core\Model\LocaleInterface $locale
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logging\Model\Archive $archive,
        \Magento\Core\Model\LocaleInterface $locale
    ) {
        parent::__construct($entityFactory);
        $basePath = $archive->getBasePath();
        $file = new \Magento\Io\File();
        $file->setAllowCreateFolders(true)->createDestinationDir($basePath);
        $this->addTargetDir($basePath);
        $this->_locale = $locale;
    }

    /**
     * Row generator
     * Add 'time' column as \Zend_Date object
     * Add 'timestamp' column as unix timestamp - used in date filter
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        $date = new \Zend_Date(str_replace('.csv', '', $row['basename']), 'yyyyMMddHH', $this->_locale->getLocaleCode());
        $row['time'] = $date;
        /**
         * Used in date filter, becouse $date contains hours
         */
        $dateWithoutHours = new \Zend_Date(str_replace('.csv', '', $row['basename']), 'yyyyMMdd',
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
