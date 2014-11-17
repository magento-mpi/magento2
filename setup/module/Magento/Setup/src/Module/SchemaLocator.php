<?php
/**
 * Modules configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Module;

use Magento\Config\SchemaLocatorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var string
     */
    protected $schemaName;

    /**
     * @param DirectoryList $directoryList
     * @param string $schemaName
     */
    public function __construct(
        DirectoryList $directoryList,
        $schemaName = 'module.xsd'
    ) {
        $this->directoryList = $directoryList;
        $this->schemaName = $schemaName;
    }

    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->directoryList->getPath(DirectoryList::LIB_INTERNAL)
            . '/Magento/Framework/Module/etc/' . $this->schemaName;
    }
}
