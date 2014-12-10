<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model;

interface FactoryInterface
{
    /**
     * Return search client
     *
     * @param array $options
     * @return mixed
     */
    public function createClient(array $options = []);

    /**
     * Return search adapter
     *
     * @return \Magento\Solr\Model\AdapterInterface
     */
    public function createAdapter();
}
