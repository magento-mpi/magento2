<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Archive
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock class to work with gz archives
 */
namespace Magento\Archive;

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