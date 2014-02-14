<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Output;


interface ConfigInterface
{
    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isEnabled($moduleName);

    /**
     * Retrieve module enabled specific path
     *
     * @param string $path Fully-qualified config path
     * @return boolean
     */
    public function isSetFlag($path);
}
