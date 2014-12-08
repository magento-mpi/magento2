<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

/**
 * Dummy layout argument data source object
 */
class DataSource extends \Magento\Framework\Data\Collection
{
    /**
     * Property which stores all updater calls
     *
     * @var array
     */
    protected $_calls = [];

    /**
     * Return current updater calls
     *
     * @return array
     */
    public function getUpdaterCall()
    {
        return $this->_calls;
    }

    /**
     * Set updater calls
     *
     * @param array $calls
     * @return \Magento\Core\Model\DataSource
     */
    public function setUpdaterCall(array $calls)
    {
        $this->_calls = $calls;
        return $this;
    }
}
