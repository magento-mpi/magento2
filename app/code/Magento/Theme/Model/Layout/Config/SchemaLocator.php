<?php
/**
 * Locator for page layouts XSD schemas.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Model\Layout\Config;

use Magento\Framework\App\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema;

    /**
     * @param \Magento\Framework\App\Filesystem $appFilesystem
     */
    public function __construct(Filesystem $appFilesystem)
    {
        $this->_schema = $appFilesystem->getPath(DirectoryList::LIB_INTERNAL)
            . '/Magento/Framework/View/PageLayout/etc/layouts.xsd';
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Get path to per file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->_schema;
    }
}
