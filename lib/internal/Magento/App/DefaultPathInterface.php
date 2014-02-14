<?php
/**
 * Abstract application router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface DefaultPathInterface
{
    /**
     * @param string $code
     * @return string
     */
    public function getPart($code);
}