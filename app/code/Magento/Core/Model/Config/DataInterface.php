<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

interface DataInterface
{
    /**
     * @param $path
     * @return mixed
     */
    public function getValue($path);
}
