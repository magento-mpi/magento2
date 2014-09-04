<?php
/**
 * Mapper class. Maps library request to specific adapter dependent query
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\Search\RequestInterface;

class Mapper
{
    /**
     * Build adapter dependent query
     *
     * @param RequestInterface $request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return mixed
     */
    public function buildQuery(RequestInterface $request)
    {
    }
}
