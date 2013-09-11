<?php
/**
 * Search client factory interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Client;

interface FactoryInterface
{
    /**
     * Return search client interface
     *
     * @param $options
     * @return mixed
     */
    public function createClient($options);
}
