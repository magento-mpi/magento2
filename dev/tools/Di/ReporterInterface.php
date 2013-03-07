<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\Di;

interface ReporterInterface
{
    public function report();

    public function addSuccess($className);

    public function addError($className);
}