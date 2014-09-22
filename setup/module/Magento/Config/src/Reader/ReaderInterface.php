<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Config\Reader;

interface ReaderInterface
{
    /**
     * Read configuration scope
     *
     * @return array
     */
    public function read();
}
