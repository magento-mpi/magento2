<?php
/**
 * Search client factory interface
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Client;

interface FactoryInterface
{
    /**
     * Return search client interface
     *
     * @param mixed $options
     * @return mixed
     */
    public function createClient($options);
}
