<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class that implements CRUP tests for \Magento\Model\AbstractModel based objects
 */
namespace Magento\TestFramework;

class Entity
{
    /**
     * @var \Magento\Model\AbstractModel
     */
    protected $_model;

    protected $_updateData;

    public function __construct(\Magento\Model\AbstractModel $model, array $updateData)
    {
        $this->_model = $model;
        $this->_updateData = $updateData;
    }

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
     * @return \Magento\Model\AbstractModel
     */
    protected function _getEmptyModel()
    {
        $modelClass = get_class($this->_model);
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($modelClass);
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
                $value,
                $model->getDataUsingMethod($key),
                'CRUD Update "' . $key . '" error'
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
