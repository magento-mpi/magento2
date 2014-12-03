<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Di\Compiler\Config;

interface WriterInterface
{
    /**
     * Writes config in storage
     *
     * @param string $areaCode
     * @param array $config
     * @return void
     */
    public function write($areaCode, array $config);
}
