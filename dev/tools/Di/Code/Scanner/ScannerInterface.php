<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

interface ScannerInterface
{
    /**
     * @return array
     */
    public function collectEntities();
}