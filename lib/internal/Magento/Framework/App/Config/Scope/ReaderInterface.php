<?php
/**
 * Scope Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Config\Scope;

interface ReaderInterface
{
    /**
     * Read configuration scope
     *
     * @return array
     */
    public function read();
}
