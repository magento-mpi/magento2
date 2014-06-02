<?php
/**
 * Abstract application router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

interface DefaultPathInterface
{
    /**
     * @param string $code
     * @return string
     */
    public function getPart($code);
}
