<?php
/**
 * Test OAuth consumer
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_OAuth_Model_TokenTest extends Magento_TestCase
{
    /**
     * Token fixture data
     *
     * @var array
     */
    protected $_tokenData;

    /**
     * Consumer fixture data
     *
     * @var array
     */
    protected $_consumerData;

    /**
     * Get token data
     *
     * @param string|null $key
     * @return array
     */
    protected function _getFixtureTokenData($key = null)
    {
        if (null == $this->_tokenData) {
            $this->_tokenData = require dirname(__FILE__) . '/_fixtures/tokenData.php';
        }
        if ($key) {
            return $this->_tokenData[$key];
        } else {
            return $this->_tokenData;
        }
    }

    /**
     * Get consumer data
     *
     * @param string|null $key
     * @return array
     */
    protected function _getFixtureConsumerData($key = null)
    {
        if (null == $this->_consumerData) {
            $this->_consumerData = require dirname(__FILE__) . '/_fixtures/consumerData.php';
        }
        if ($key) {
            return $this->_consumerData[$key];
        } else {
            return $this->_consumerData;
        }
    }

    /**
     * Test CRUD
     *
     * @return void
     */
    public function testCrud()
    {
        $consumer = new Mage_OAuth_Model_Consumer();
        $consumer->setData($this->_getFixtureConsumerData('create'));
        $consumer->save();
        $this->addModelToDelete($consumer);
        $consumerId = $consumer->getId();

        $customerId = $this->getDefaultCustomer()->getId();

        $model = new Mage_OAuth_Model_Token();
        $this->addModelToDelete($model);
        $data = $this->_getFixtureTokenData();
        $data['create']['consumer_id'] = $consumerId;
        $data['create']['customer_id'] = $customerId;

        /**
         * Test create
         */
        $model->setData($data['create']);
        $model->save();
        $this->assertNotNull($model->getId(), 'ID cannot be null after saving.');

        /**
         * Test read
         */
        $id = $model->getId();
        $model = new Mage_OAuth_Model_Token();
        $dataCreated = $data['create'];
        $dataCreated['entity_id'] = $id;
        $model->load($id);
        $dataLoaded = $model->getData();

        unset($dataCreated['created_at'],
            $dataLoaded['created_at']);

        $this->assertEquals($dataCreated, $dataLoaded,
            'Expected data with actual loaded data is not equals.');

        /**
         * Test update
         */
        $model->addData($data['update']);
        $model->save();

        $dataUpdated = $data['update'];
        $dataUpdated['entity_id']   = $model->getId();
        $dataUpdated['admin_id']    = null;
        $dataUpdated['consumer_id'] = $consumerId;
        $dataUpdated['customer_id'] = $customerId;
        $model->load($model->getId());
        $dataLoaded = $model->getData();

        unset($dataUpdated['created_at'],
            $dataLoaded['created_at']);

        $this->assertEquals($dataUpdated, $dataLoaded,
            'Expected data with actual loaded data is not equals.');

        /**
         * Test delete
         */
        $model->delete();
        $this->assertNull($model->setId(null)->load($id)->getId(), 'ID must be null after deleting.');
    }
}
