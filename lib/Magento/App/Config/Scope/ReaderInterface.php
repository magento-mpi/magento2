<?php
/**
 * Scope Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Scope;

interface ReaderInterface
{
    /**
     * Read configuration scope
     */
    public function read();
}
