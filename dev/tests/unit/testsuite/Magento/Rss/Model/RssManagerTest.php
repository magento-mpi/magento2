<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Model;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RssManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rss\Model\RssManager
     */
    protected $rssManager;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->rssManager = $objectManagerHelper->getObject(
            'Magento\Rss\Model\RssManager',
            [
                'objectManager' => $this->objectManager,
                'dataProviders' => array(
                    'rss_feed' => 'Magento\Framework\App\Rss\DataProviderInterface',
                    'bad_rss_feed' => 'Some\Class\Not\Existent',
                )
            ]
        );
    }

    public function testGetProvider()
    {
        $dataProvider = $this->getMock('Magento\Framework\App\Rss\DataProviderInterface');
        $this->objectManager->expects($this->once())->method('get')->will($this->returnValue($dataProvider));

        $this->assertInstanceOf(
             '\Magento\Framework\App\Rss\DataProviderInterface',
             $this->rssManager->getProvider('rss_feed')
        );
    }

    public function testGetProviderFirstException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->rssManager->getProvider('wrong_rss_feed');
    }

    public function testGetProviderSecondException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->rssManager->getProvider('bad_rss_feed');
    }
}
