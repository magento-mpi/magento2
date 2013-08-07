<?php
/**
 * Search client factory interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Search_Model_Client_FactoryInterface
{
    /**
     * Return search client interface
     *
     * @param $options
     * @return mixed
     */
    public function createClient($options);
}
