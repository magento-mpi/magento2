<?php
/**
 * Test OAuth consumer
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_Oauth_Model_TokenTest extends Magento_TestCase
{
    /**
     * 15 years, sec
     */
    const TOKEN_TIME_FOR_DELETE = 473040000;

    /**
     * Count of new token items
     */
    const NEW_TOKEN_COUNT = 5;

    /**
     * Count of old token items
     */
    const OLD_TOKEN_COUNT = 30;

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
     * Set up a consumer data fixture
     */
    protected function setUp()
    {
        /** @var $consumer Mage_Oauth_Model_Consumer */
        $consumer = Mage::getModel('Mage_Oauth_Model_Consumer');
        $consumer->setData($this->_getFixtureConsumerData('create'))->save();

        $this->setFixture('consumer', $consumer);

        // Delete consumer item after test
        $this->addModelToDelete($consumer, true);

        parent::setUp();
    }

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
     * Create several tokens for clean up test
     *
     * @return Mage_Oauth_Model_TokenTest
     */
    protected function _createTokensForCleanUp()
    {
        /** @var $consumer Mage_Oauth_Model_Consumer */
        $consumer = $this->getFixture('consumer');
        /** @var $tokenResource Mage_Oauth_Model_Resource_Token */
        $tokenResource = Mage::getResourceModel('Mage_Oauth_Model_Resource_Token');
        /** @var $token Mage_Oauth_Model_Token */
        $token = Mage::getModel('Mage_Oauth_Model_Token');

        // Generate new token items
        $i = 0;
        while ($i++ < self::NEW_TOKEN_COUNT) {
            $token->setData(array(
                'consumer_id'  => $consumer->getId(),
                'type'         => Mage_Oauth_Model_Token::TYPE_REQUEST,
                'token'        => md5(mt_rand()),
                'secret'       => md5(mt_rand()),
                'callback_url' => Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED,
                'created_at'   => Varien_Date::now()
            ));
            $tokenResource->save($token); // save via resource to avoid object afterSave() calls
        }
        // Generate old token items
        $i = 0;
        while ($i++ < self::OLD_TOKEN_COUNT) {
            $token->setData(array(
                'consumer_id'  => $consumer->getId(),
                'type'         => Mage_Oauth_Model_Token::TYPE_REQUEST,
                'token'        => md5(mt_rand()),
                'secret'       => md5(mt_rand()),
                'callback_url' => Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED,
                'created_at'   => Varien_Date::formatDate(time() - self::TOKEN_TIME_FOR_DELETE)
            ));
            $tokenResource->save($token); // save via resource to avoid object afterSave() calls
        }
        return $this;
    }

    /**
     * Test CRUD
     *
     * @return void
     */
    public function testCrud()
    {
        /** @var $consumer Mage_Oauth_Model_Consumer */
        $consumer = $this->getFixture('consumer');
        $consumerId = $consumer->getId();

        $customerId = $this->getDefaultCustomer()->getId();

        $model = new Mage_Oauth_Model_Token();
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
        $model = new Mage_Oauth_Model_Token();
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

    /**
     * Test delete old token items by _afterSave() method
     *
     * @return void
     */
    public function testAfterSave()
    {
        $this->_createTokensForCleanUp();

        /** @var $token Mage_Oauth_Model_Token */
        $token = Mage::getModel('Mage_Oauth_Model_Token');

        /** @var $collection Mage_Oauth_Model_Resource_Token_Collection */
        $collection = $token->getCollection();

        $helper = $this->_replaceHelperWithMock('oauth', array('isCleanupProbability', 'getCleanupExpirationPeriod'));
        $helper->expects($this->once())
            ->method('isCleanupProbability')
            ->will($this->returnValue(true));

        $helper->expects($this->once())
            ->method('getCleanupExpirationPeriod')
            ->will($this->returnValue(self::TOKEN_TIME_FOR_DELETE/60));

        $collection->getFirstItem()->setKey(md5(mt_rand()))->save();
        $this->assertEquals($collection->count() - self::OLD_TOKEN_COUNT, $token->getCollection()->count());

        $this->_restoreHelper('oauth');
    }

    /**
     * Test delete old token items fail by _afterSave() method
     *
     * @return void
     */
    public function testAfterSaveFail()
    {
        $this->_createTokensForCleanUp();

        /** @var $token Mage_Oauth_Model_Token */
        $token = Mage::getModel('Mage_Oauth_Model_Token');

        /** @var $collection Mage_Oauth_Model_Resource_Token_Collection */
        $collection = $token->getCollection();

        $helper = $this->_replaceHelperWithMock('oauth', array('isCleanupProbability', 'getCleanupExpirationPeriod'));
        $helper->expects($this->once())
            ->method('isCleanupProbability')
            ->will($this->returnValue(false));

        $helper->expects($this->never())
            ->method('getCleanupExpirationPeriod');

        $collection->getFirstItem()->setKey(md5(mt_rand()))->save();
        $this->assertEquals($collection->count(), $token->getCollection()->count());

        $this->_restoreHelper('oauth');
    }

    /**
     * Test delete old token items by resource model method
     *
     * @return void
     */
    public function testDeleteOldEntries()
    {
        $this->_createTokensForCleanUp();

        /** @var $token Mage_Oauth_Model_Token */
        $token = Mage::getModel('Mage_Oauth_Model_Token');
        $count = $token->getCollection()->count();

        $token->getResource()->deleteOldEntries(self::TOKEN_TIME_FOR_DELETE/60);
        $this->assertEquals($count - self::OLD_TOKEN_COUNT, $token->getCollection()->count());
    }
}
