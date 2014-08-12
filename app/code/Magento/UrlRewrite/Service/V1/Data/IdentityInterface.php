<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1\Data;

interface IdentityInterface
{
    /**
     * @return string
     */
    public function getFilterType();
}