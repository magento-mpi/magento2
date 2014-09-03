<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Fixture;

use Magento\UrlRewrite\Test\Fixture\UrlRewrite as ParentUrlRewrite;

/**
 * Class UrlRewrite
 */
class UrlRewrite extends ParentUrlRewrite
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Cms\Test\Repository\UrlRewrite';
}
