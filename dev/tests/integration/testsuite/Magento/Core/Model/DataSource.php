<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dummy layout argument data source object
 */
namespace Magento\Core\Model;

class DataSource extends \Magento\Data\Collection
{
    /**
     * Property which stores all updater calls
     *
     * @var array
     */
    protected $_calls = array();

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
