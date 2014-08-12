<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Service\V1\Data;


/**
 * Url rewrite search filter
 */
interface FilterInterface
{
    /**
     * @return array
     */
    public function getFilter();
}