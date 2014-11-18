<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\ObjectManager\ConfigLoader;

class Compiled extends \Magento\Framework\App\ObjectManager\ConfigLoader
{
    public function __construct()
    {
    }

    public function load($area)
    {
        $data = \unserialize(\file_get_contents(BP . '/var/di/' . $area . '.ser'));
        return $data;
    }
}
