<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
