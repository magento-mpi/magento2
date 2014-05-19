<?php

namespace Magento\Catalog\Service\V1;

interface ProductServiceInterface
{
    /**
     * Save product process
     *
     * @param Data\Product $product
     */
    public function save(Data\Product $product);
}
