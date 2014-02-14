<?php
/**
 * Inline Translation config interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate\Inline;

interface ConfigInterface
{
    /**
     * Check whether inline translation is enabled
     *
     * @param int|null $scope
     * @return bool
     */
    public function isActive($scope = null);


    /**
     * Check whether allowed client ip for inline translation
     *
     * @param mixed $scope
     * @return bool
     */
    public function isDevAllowed($scope = null);
}
