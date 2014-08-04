<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Config;

interface FileResolverInterface
{
    /**
     * Retrieve the list of configuration files with given name
     *
     * @param string $filename
     * @return array
     */
    public function get($filename);
}
