<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Oauth
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test OAuth Nonce
 */
class Mage_Oauth_Model_NonceTest extends Magento_TestCase
{
    /**
     * 15 years, sec
     */
    const NONCE_TIME_FOR_DELETE = 473040000;

    /**
     * Count of new nonce items
     */
    const NEW_NONCE_COUNT = 5;

    /**
     * Count of old nonce items
     */
    const OLD_NONCE_COUNT = 30;

    /**
     * Create nonce items for tests
     *
     * @return void
     */
    protected function setUp()
    {
        /** @var $nonceResource Mage_Oauth_Model_Resource_Nonce */
        $nonceResource = Mage::getResourceModel('Mage_Oauth_Model_Resource_Nonce');
        /** @var $nonce Mage_Oauth_Model_Nonce */
        $nonce = Mage::getModel('Mage_Oauth_Model_Nonce');

        $time = time();

        // Generate new nonce items
        $i = 0;
        while ($i++ < self::NEW_NONCE_COUNT) {
            $nonce->setData(array('nonce' => md5(mt_rand()), 'timestamp' => $time));
            $nonceResource->save($nonce); // save via resource to avoid object afterSave() calls
        }
        // Generate old nonce items
        $i = 0;
        while ($i++ < self::OLD_NONCE_COUNT) {
            $nonce->setData(array('nonce' => md5(mt_rand()), 'timestamp' => $time - self::NONCE_TIME_FOR_DELETE));
            $nonceResource->save($nonce); // save via resource to avoid object afterSave() calls
        }
        parent::setUp();
    }

    /**
     * Test delete old nonce items by resource model method
     *
     * @return void
     */
    public function testDeleteOldEntries()
    {
        /** @var $nonce Mage_Oauth_Model_Nonce */
        $nonce = Mage::getModel('Mage_Oauth_Model_Nonce');

        // Nonce items count before delete action
        $count = $nonce->getCollection()->count();

        $nonce->getResource()->deleteOldEntries(self::NONCE_TIME_FOR_DELETE/60);
        $this->assertEquals($count - self::OLD_NONCE_COUNT, $nonce->getCollection()->count());
    }

    /**
     * Test delete old nonce items by _afterSave() method
     *
     * @depends testDeleteOldEntries
     * @return void
     */
    public function testAfterSave()
    {
        /** @var $nonce Mage_Oauth_Model_Nonce */
        $nonce = Mage::getModel('Mage_Oauth_Model_Nonce');

        // Nonce items count before delete action
        $count = $nonce->getCollection()->count();

        $helper = $this->_replaceHelperWithMock('Mage_Oauth_Helper_Data',
            array('isCleanupProbability', 'getCleanupExpirationPeriod'));
        $helper->expects($this->once())
            ->method('isCleanupProbability')
            ->will($this->returnValue(true));

        $helper->expects($this->once())
            ->method('getCleanupExpirationPeriod')
            ->will($this->returnValue(self::NONCE_TIME_FOR_DELETE/60));

        // Create new nonce item for _afterSave() dispatch
        $nonce->setNonce(md5(mt_rand()))
            ->setTimestamp(time() - self::NONCE_TIME_FOR_DELETE)
            ->save();

        $this->assertEquals($count - self::OLD_NONCE_COUNT, $nonce->getCollection()->count());
        $this->_restoreHelper('Mage_Oauth_Helper_Data');
    }

    /**
     * Test delete old nonce items fail by _afterSave() method
     *
     * @depends testDeleteOldEntries
     * @return void
     */
    public function testAfterSaveFail()
    {
        /** @var $nonce Mage_Oauth_Model_Nonce */
        $nonce = Mage::getModel('Mage_Oauth_Model_Nonce');

        // Nonce items count before delete action
        $count = $nonce->getCollection()->count();

        $helper = $this->_replaceHelperWithMock('Mage_Oauth_Helper_Data',
            array('isCleanupProbability', 'getCleanupExpirationPeriod'));
        $helper->expects($this->once())
            ->method('isCleanupProbability')
            ->will($this->returnValue(false));

        $helper->expects($this->never())
            ->method('getCleanupExpirationPeriod');

        // Create new nonce item for _afterSave() dispatch
        $nonce->setNonce(md5(mt_rand()))
            ->setTimestamp(time() - self::NONCE_TIME_FOR_DELETE)
            ->save();

        $this->assertEquals($count + 1, $nonce->getCollection()->count());
        $this->_restoreHelper('Mage_Oauth_Helper_Data');

        // Delete excess nonce items
        $nonce->getResource()->deleteOldEntries(self::NONCE_TIME_FOR_DELETE/60);
    }
}
