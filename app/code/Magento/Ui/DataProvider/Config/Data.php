<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui\DataProvider\Config;

/**
 * Class Data
 */
class Data extends \Magento\Framework\Config\Data
{
    /**
     * @param Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     */
    public function __construct(
        Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, 'data_source');
    }

    /**
     * @param string $name
     * @return array|mixed|null
     */
    public function getDataSource($name)
    {
        return $this->get($name);
    }
}
