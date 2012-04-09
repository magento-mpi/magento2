<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Oauth
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $nonceResource = Mage::getResourceModel('oauth/nonce');
        /** @var $nonce Mage_Oauth_Model_Nonce */
        $nonce = Mage::getModel('oauth/nonce');

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
        $nonce = Mage::getModel('oauth/nonce');

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
        $nonce = Mage::getModel('oauth/nonce');

        // Nonce items count before delete action
        $count = $nonce->getCollection()->count();

        $helper = $this->_replaceHelperWithMock('oauth', array('isCleanupProbability', 'getCleanupExpirationPeriod'));
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
        $this->_restoreHelper('oauth');
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
        $nonce = Mage::getModel('oauth/nonce');

        // Nonce items count before delete action
        $count = $nonce->getCollection()->count();

        $helper = $this->_replaceHelperWithMock('oauth', array('isCleanupProbability', 'getCleanupExpirationPeriod'));
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
        $this->_restoreHelper('oauth');

        // Delete excess nonce items
        $nonce->getResource()->deleteOldEntries(self::NONCE_TIME_FOR_DELETE/60);
    }
}
