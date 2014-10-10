<?php
/**
 * Search client factory interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
