<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model\Store;

use Magento\TestFramework\Helper\Bootstrap;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Store\Group
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Store\Group');
    }

    public function testSetGetWebsite()
    {
        $this->assertFalse($this->_model->getWebsite());
        $website = Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getWebsite();
        $this->_model->setWebsite($website);
        $actualResult = $this->_model->getWebsite();
        $this->assertSame($website, $actualResult);
    }

    /**
     * Tests that getWebsite returns the default site when defaults are passed in for id
     */
    public function testGetWebsiteDefault()
    {
        $this->assertFalse($this->_model->getWebsite());
        $website = Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getWebsite();
        $this->_model->setWebsite($website);
        // Empty string should get treated like no parameter
        $actualResult = $this->_model->getWebsite('');
        $this->assertSame($website, $actualResult);
        // Null string should get treated like no parameter
        $actualResult = $this->_model->getWebsite(null);
        $this->assertSame($website, $actualResult);
    }
}
