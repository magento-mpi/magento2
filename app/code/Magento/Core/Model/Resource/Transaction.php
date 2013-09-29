<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource transaction model
 *
 * @todo need collect conection by name
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource;

class Transaction
{
    /**
     * Objects which will be involved to transaction
     *
     * @var array
     */
    protected $_objects = array();

    /**
     * Transaction objects array with alias key
     *
     * @var array
     */
    protected $_objectsByAlias = array();

    /**
     * Callbacks array.
     *
     * @var array
     */
    protected $_beforeCommitCallbacks = array();
    /**
     * Begin transaction for all involved object resources
     *
     * @return \Magento\Core\Model\Resource\Transaction
     */
    protected function _startTransaction()
    {
        foreach ($this->_objects as $object) {
            $object->getResource()->beginTransaction();
        }
        return $this;
    }

    /**
     * Commit transaction for all resources
     *
     * @return \Magento\Core\Model\Resource\Transaction
     */
    protected function _commitTransaction()
    {
        foreach ($this->_objects as $object) {
            $object->getResource()->commit();
        }
        return $this;
    }

    /**
     * Rollback transaction
     *
     * @return \Magento\Core\Model\Resource\Transaction
     */
    protected function _rollbackTransaction()
    {
        foreach ($this->_objects as $object) {
            $object->getResource()->rollBack();
        }
        return $this;
    }

    /**
     * Run all configured object callbacks
     *
     * @return \Magento\Core\Model\Resource\Transaction
     */
    protected function _runCallbacks()
    {
        foreach ($this->_beforeCommitCallbacks as $callback) {
            call_user_func($callback);
        }
        return $this;
    }

    /**
     * Adding object for using in transaction
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $alias
     * @return \Magento\Core\Model\Resource\Transaction
     */
    public function addObject(\Magento\Core\Model\AbstractModel $object, $alias='')
    {
        $this->_objects[] = $object;
        if (!empty($alias)) {
            $this->_objectsByAlias[$alias] = $object;
        }
        return $this;
    }

    /**
     * Add callback function which will be called before commit transactions
     *
     * @param callback $callback
     * @return \Magento\Core\Model\Resource\Transaction
     */
    public function addCommitCallback($callback)
    {
        $this->_beforeCommitCallbacks[] = $callback;
        return $this;
    }

    /**
     * Initialize objects save transaction
     *
     * @return \Magento\Core\Model\Resource\Transaction
     * @throws \Exception
     */
    public function save()
    {
        $this->_startTransaction();
        $error     = false;

        try {
            foreach ($this->_objects as $object) {
                $object->save();
            }
        } catch (\Exception $e) {
            $error = $e;
        }

        if ($error === false) {
            try {
                $this->_runCallbacks();
            } catch (\Exception $e) {
                $error = $e;
            }
        }

        if ($error) {
            $this->_rollbackTransaction();
            throw $error;
        } else {
            $this->_commitTransaction();
        }

        return $this;
    }

    /**
     * Initialize objects delete transaction
     *
     * @return \Magento\Core\Model\Resource\Transaction
     * @throws \Exception
     */
    public function delete()
    {
        $this->_startTransaction();
        $error = false;

        try {
            foreach ($this->_objects as $object) {
                $object->delete();
            }
        } catch (\Exception $e) {
            $error = $e;
        }

        if ($error === false) {
            try {
                $this->_runCallbacks();
            } catch (\Exception $e) {
                $error = $e;
            }
        }

        if ($error) {
            $this->_rollbackTransaction();
            throw $error;
        } else {
            $this->_commitTransaction();
        }
        return $this;
    }

}
