<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config;

interface DataInterface
{
    /**
     * @param string|null $path
     * @return string|array
     */
    public function getValue($path);
}
