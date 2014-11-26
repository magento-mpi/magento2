<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Dictionary\Loader;

/**
 * Dictionary loader interface
 */
interface FileInterface
{
    /**
     * Load dictionary
     *
     * @param string $file
     * @return \Magento\Tools\I18n\Dictionary
     * @throws \InvalidArgumentException
     */
    public function load($file);
}
