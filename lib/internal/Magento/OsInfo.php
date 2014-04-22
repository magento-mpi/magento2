<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_OSInfo
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

/**
 * Wrapper on PHP_OS constant
 */
class OsInfo
{
    /**
     * Operation system
     *
     * @var string
     */
    protected $os;

    /**
     * Initialize os
     */
    public function __construct()
    {
        $this->os = PHP_OS;
    }

    /**
     * Check id system is Windows
     *
     * @return bool
     */
    public function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
