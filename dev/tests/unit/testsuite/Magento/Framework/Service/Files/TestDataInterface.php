<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Files;

interface TestDataInterface
{
    public function getId();

    public function getAddress();

    public function isDefaultShipping();

    public function isRequiredBilling();
}
