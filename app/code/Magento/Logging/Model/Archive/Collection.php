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
namespace Magento\Logging\Model\Archive;

use Magento\Framework\App\Filesystem\DirectoryList;

class Collection extends \Magento\Framework\Data\Collection\Filesystem
{
    /**
     * Filenames regex filter
     *
     * @var string
     */
    protected $_allowedFilesMask = '/^[a-z0-9\.\-\_]+\.csv$/i';

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * Set target dir for scanning
     *
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logging\Model\Archive $archive
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logging\Model\Archive $archive,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\App\Filesystem $filesystem
    ) {
        parent::__construct($entityFactory);
        $basePath = $archive->getBasePath();
        $dir = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $dir->create($dir->getRelativePath($basePath));
        $this->addTargetDir($basePath);
        $this->_localeResolver = $localeResolver;
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
        $date = new \Magento\Framework\Stdlib\DateTime\Date(
            str_replace('.csv', '', $row['basename']),
            'yyyyMMddHH',
            $this->_localeResolver->getLocaleCode()
        );
        $row['time'] = $date;
        /**
         * Used in date filter, becouse $date contains hours
         */
        $dateWithoutHours = new \Magento\Framework\Stdlib\DateTime\Date(
            str_replace('.csv', '', $row['basename']),
            'yyyyMMdd',
            $this->_localeResolver->getLocaleCode()
        );
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
            $rowValue = $row['timestamp'];
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
            $rowValue = $row['timestamp'];
        }
        return $rowValue < $filterValue;
    }
}
