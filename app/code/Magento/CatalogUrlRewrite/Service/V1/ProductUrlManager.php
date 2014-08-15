<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\UrlRewrite\Service\V1\UrlManager;

class ProductUrlManager extends UrlManager
{
    /**
     * Find row by specific data
     *
     * @param array $data
     * @return mixed
     */
    public function findByData(array $data)
    {
        return $this->storage->findByData($data);
    }
}
