<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\Search\RequestInterface;
use Magento\Framework\DB\Select;

/**
 * Build base Query for Index
 */
interface IndexBuilderInterface
{
    /**
     * Build index query
     *
     * @param RequestInterface $request
     * @return Select
     */
    public function build(RequestInterface $request);
}
