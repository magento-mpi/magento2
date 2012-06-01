<?php
/**
 * Test OAuth consumer
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_Oauth_Model_ConsumerTest extends Magento_TestCase
{
    /**
     * Fixture data
     *
     * @var array
     */
    protected $_fixture;

    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixtureData()
    {
        if (null === $this->_fixture) {
            $this->_fixture = require dirname(__FILE__) . '/_fixture/consumer_data.php';
        }
        return $this->_fixture;
    }

    /**
     * Test validation URL failure
     *
     * @return void
     */
    public function testCrud()
    {
        $model = new Mage_Oauth_Model_Consumer();
        $this->addModelToDelete($model);
        $data = $this->_getFixtureData();

        /**
         * Test create
         */
        $model->setData($data['create']);
        $this->assertEquals($model->validate(), true, 'Expected success validation.');
        $model->save();
        $this->assertNotNull($model->getId(), 'ID cannot be null after saving.');

        /**
         * Test read
         */
        $id = $model->getId();
        $model = new Mage_Oauth_Model_Consumer();
        $dataCreated = $data['expected_create'];
        $dataCreated['entity_id'] = $id;
        $model->load($id);
        $dataLoaded = $model->getData();
        $this->assertTrue(strtotime($dataLoaded['created_at']) > 0, 'Created time is not set.');

        unset($dataLoaded['created_at']);
        unset($dataLoaded['updated_at']);
        unset($dataCreated['created_at']);

        $this->assertEquals($dataCreated, $dataLoaded,
            'Expected data with actual loaded data is not equals.');

        /**
         * Test update
         */
        $model->addData($data['update']);
        $this->assertEquals($model->validate(), true, 'Expected success validation.');
        $model->save();

        $dataUpdated = $data['expected_update'];
        $dataUpdated['updated_at'] = null;
        $dataUpdated['entity_id'] = $model->getId();
        $model->load($model->getId());
        $dataLoaded = $model->getData();
        $this->assertTrue(strtotime($dataLoaded['created_at']) > 0, 'Created time is not set.');
        $this->assertTrue(strtotime($dataLoaded['updated_at']) > 0, 'Updated time is not set.');

        unset($dataUpdated['created_at']);
        unset($dataLoaded['created_at']);
        unset($dataUpdated['updated_at']);
        unset($dataLoaded['updated_at']);

        $this->assertEquals($dataUpdated, $dataLoaded,
            'Expected data with actual loaded data is not equals.');

        /**
         * Test delete
         */
        $model->delete();
        $this->assertNull($model->setId(null)->load($id)->getId(), 'ID must be null after deleting.');
        $this->addModelToDelete($model);
    }
}
