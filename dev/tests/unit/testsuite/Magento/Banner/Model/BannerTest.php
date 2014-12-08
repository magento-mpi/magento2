<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Model;

class BannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Model\Banner
     */
    protected $banner;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->banner = $objectManager->getObject('Magento\Banner\Model\Banner');
    }

    protected function tearDown()
    {
        $this->banner = null;
    }

    public function testGetIdentities()
    {
        $id = 1;
        $this->banner->setId($id);
        $this->assertEquals(
            [\Magento\Banner\Model\Banner::CACHE_TAG . '_' . $id],
            $this->banner->getIdentities()
        );
    }
}
