<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock class to work with gz archives
 */
namespace Magento\Framework\Archive;

class Gz
{
    /**
     * Mock pack file by GZ compressor.
     *
     * @param $source
     * @param $destination
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function pack($source, $destination)
    {
        return '\unexistingpath';
    }
}
