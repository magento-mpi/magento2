<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml abstract  dashboard helper.
 */
namespace Magento\Backend\Helper\Dashboard;

abstract class AbstractDashboard extends \Magento\Core\Helper\Data
{
    /**
     * Helper collection
     *
     * @var \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\Eav\Model\Entity\Collection\Abstract|array
     */
    protected  $_collection;

    /**
     * Parameters for helper
     *
     * @var array
     */
    protected  $_params = array();

    public function getCollection()
    {
        if (is_null($this->_collection)) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    abstract protected  function _initCollection();

    /**
     * Returns collection items
     *
     * @return array
     */
    public function getItems()
    {
        return is_array($this->getCollection()) ? $this->getCollection() : $this->getCollection()->getItems();
    }

    public function getCount()
    {
        return sizeof($this->getItems());
    }

    public function getColumn($index)
    {
        $result = array();
        foreach ($this->getItems() as $item) {
            if (is_array($item)) {
                if (isset($item[$index])) {
                    $result[] = $item[$index];
                } else {
                    $result[] = null;
                }
            } elseif ($item instanceof \Magento\Object) {
                $result[] = $item->getData($index);
            } else {
                $result[] = null;
            }
        }
        return $result;
    }

    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
    }

    public function setParams(array $params)
    {
        $this->_params = $params;
    }

    public function getParam($name)
    {
        if (isset($this->_params[$name])) {
            return $this->_params[$name];
        }

        return null;
    }

    public function getParams()
    {
        return $this->_params;
    }
}
