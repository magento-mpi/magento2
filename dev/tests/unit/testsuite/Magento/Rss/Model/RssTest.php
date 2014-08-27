<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Model;

use Magento\TestFramework\Helper\ObjectManager;

class RssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rss\Model\Rss
     */
    private $model;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->model = $helper->getObject('Magento\Rss\Model\Rss');
    }

    public function testCreateRssXml()
    {
        $this->model->_addHeader(['title' => 'someTitle', 'link' => 'someLink', 'charset' => 'utf8']);
        $result = $this->model->createRssXml();

        $this->assertContains('<?xml version="1.0" encoding="utf8"?>', $result);
    }

    public function testCreateRssXmlError()
    {
        $this->model->_addHeader(['test']);
        $this->assertEquals('Error in processing xml. title key is missing', $this->model->createRssXml());
    }
}
