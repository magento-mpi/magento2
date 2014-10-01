<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model;

interface QueryFactoryInterface
{
    /**
     * @return QueryInterface
     */
    public function getQuery();
}
