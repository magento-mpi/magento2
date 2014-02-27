<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

/**
 * Import entity interface
 */
interface ImportEntityInterface
{
    /**
     * Source path to original file
     *
     * @return string
     */
    public function getOriginalFile();

    /**
     * Change timestamp for original file
     *
     * @return int
     */
    public function getOriginalMtime();
}
