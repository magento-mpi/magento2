<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

use \Magento\Filesystem;

/**
 * Import entity interface
 */
interface ImportEntityInterface
{
    /**
     * @return string
     */
    public function getOriginalFile();

    /**
     * @return int
     */
    public function getOriginalMtime();

    /**
     * @return bool
     */
    public function isValid();
}
