<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Adminhtml\System\Config\Source;

/**
 * @magentoAppArea adminhtml
 */
class EngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Solr\Model\Adminhtml\System\Config\Source\Engine
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Solr\Model\Adminhtml\System\Config\Source\Engine'
        );
    }

    public function testToOptionArray()
    {
        $this->markTestSkipped('Solr module disabled');
        $options = $this->_model->toOptionArray();
        $this->assertNotEmpty($options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertTrue(class_exists($option['value']));
        }
    }
}
