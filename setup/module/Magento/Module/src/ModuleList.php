<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module;

use Magento\Module\Reader\Filesystem;

class ModuleList implements ModuleListInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param Filesystem $reader
     */
    public function __construct(Filesystem $reader)
    {
        $this->data = $reader->read();
    }

    /**
     * Get configuration of all declared active modules
     *
     * @return array
     */
    public function getModules()
    {
        return $this->data;
    }

    /**
     * Get module configuration
     *
     * @param string $moduleName
     * @return array|null
     */
    public function getModule($moduleName)
    {
        return isset($this->data[$moduleName]) ? $this->data[$moduleName] : null;
    }
}
