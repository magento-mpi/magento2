<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test \Magento\Customer\Model\Config\Share
 */
class ShareTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSharedWebsiteIds()
    {
        /** @var Share $share */
        $share = Bootstrap::getObjectManager()->get('Magento\Customer\Model\Config\Share');

        $websiteIds = $share->getSharedWebsiteIds(42);

        $this->assertEquals(array(42), $websiteIds);
    }

    /**
     * @magentoDataFixture Magento/Core/_files/second_third_store.php
     * @magentoConfigFixture current_store customer/account_share/scope 0
     */
    public function testGetSharedWebsiteIdsMultipleSites()
    {
        /** @var Share $share */
        $share = Bootstrap::getObjectManager()->get('Magento\Customer\Model\Config\Share');
        $expectedIds = array(1);
        /** @var \Magento\Core\Model\Website $website */
        $website = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Core\Model\Website');
        $expectedIds[] = $website->load('secondwebsite')->getId();
        $expectedIds[] = $website->load('thirdwebsite')->getId();

        $websiteIds = $share->getSharedWebsiteIds(42);

        $this->assertEquals($expectedIds, $websiteIds);
    }
}
