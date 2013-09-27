<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model;

interface FactoryInterface
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
     * @return \Magento\Search\Model\AdapterInterface
     */
    public function createAdapter();
}
