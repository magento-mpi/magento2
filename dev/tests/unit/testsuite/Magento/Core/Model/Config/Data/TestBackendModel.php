<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Data;

class TestBackendModel implements \Magento\Core\Model\Config\Data\BackendModelInterface
{
    public function processValue($value)
    {
        return $value;
    }
}
