<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework;

/**
 * Class that implements CRUD tests for \Magento\Core\Model\AbstractModel based objects
 */
class Entity
{
    /**
     * @var \Magento\Core\Model\AbstractModel
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_updateData;

    /**
     * @var string
     */
    protected $_modelClass;

    /**
     * @param \Magento\Core\Model\AbstractModel $model
     * @param array $updateData
     * @param string|null $modelClass Class of a model to use when creating new instances, or NULL for auto-detection
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\Core\Model\AbstractModel $model, array $updateData, $modelClass = null)
    {
        $this->_model       = $model;
        $this->_updateData  = $updateData;
        if ($modelClass) {
            if (!$model instanceof $modelClass) {
                throw new \InvalidArgumentException("Class '$modelClass' is irrelevant to the tested model.");
            }
            $this->_modelClass = $modelClass;
        } else {
            $this->_modelClass = get_class($this->_model);
        }
    }

    /**
     * Test Create -> Read -> Update -> Delete operations
     */
    public function testCrud()
    {
        $this->_testCreate();
        try {
            $this->_testRead();
            $this->_testUpdate();
            $this->_testDelete();
        } catch (\Exception $e) {
            $this->_model->delete();
            throw $e;
        }
    }

    /**
     * Retrieve new instance of not yet loaded model
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _getEmptyModel()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($this->_modelClass);
    }

    protected function _testCreate()
    {
        if ($this->_model->getId()) {
            \PHPUnit_Framework_Assert::fail("Can't run creation test for models with defined id");
        }
        $this->_model->save();
        \PHPUnit_Framework_Assert::assertNotEmpty($this->_model->getId(), 'CRUD Create error');
    }

    protected function _testRead()
    {
        $model = $this->_getEmptyModel();
        $model->load($this->_model->getId());
        \PHPUnit_Framework_Assert::assertEquals($this->_model->getId(), $model->getId(), 'CRUD Read error');
    }

    protected function _testUpdate()
    {
        foreach ($this->_updateData as $key => $value) {
            $this->_model->setDataUsingMethod($key, $value);
        }
        $this->_model->save();

        $model = $this->_getEmptyModel();
        $model->load($this->_model->getId());
        foreach ($this->_updateData as $key => $value) {
            \PHPUnit_Framework_Assert::assertEquals(
                $value, $model->getDataUsingMethod($key), 'CRUD Update "'.$key.'" error'
            );
        }
    }

    protected function _testDelete()
    {
        $modelId = $this->_model->getId();
        $this->_model->delete();

        $model = $this->_getEmptyModel();
        $model->load($modelId);
        \PHPUnit_Framework_Assert::assertEmpty($model->getId(), 'CRUD Delete error');
    }
}
