<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Search_Model_FactoryInterface
{
    /**
     * Return search client
     *
     * @param array $options
     * @return mixed
     */
    public function createClient(array $options = array());

    /**
     * Return search adapter
     *
     * @return Magento_Search_Model_AdapterInterface
     */
    public function createAdapter();
}
