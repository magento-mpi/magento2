<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Data;

class TestBackendModel implements \Magento\App\Config\Data\ProcessorInterface
{
    public function processValue($value)
    {
        return $value;
    }
}
