<?php
/**
 * Test Mage Api Session model
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento PaaS Team <paas-team@magentocommerce.com>
 */
class Mage_Api_Model_SessionTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test vulnerability on session start
     *
     * @return Mage_Api_Model_SessionTest
     */
    public function testSessionStartVulnerability()
    {
        $model = new Mage_Api_Model_Session();
        //call start with empty session name
        $model->start();
        //try assert equals session id by old algorithm with real session id
        $this->assertTrue(
            md5(time()) != $model->getSessionId(),
            'Session API starting has vulnerability.');

        return $this;
    }
}
