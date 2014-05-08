<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model;

class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $model;

    /**
     * @magentoDbIsolation enabled
     * @dataProvider generateIdentifierFromTitleDataProvider
     */
    public function testGenerateIdentifierFromTitle($data, $expectedIdentifier)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Cms\Model\Page $page */
        $page = $objectManager->create('Magento\Cms\Model\Page');
        $page->setData($data);
        $page->save();
        $this->assertEquals($expectedIdentifier, $page->getIdentifier());
    }

    public function generateIdentifierFromTitleDataProvider()
    {
        return array(
            array('data' => array('title' => 'Test title'), 'expectedIdentifier' => 'test-title'),
            array(
                'data' => array('title' => 'Кирилический заголовок'),
                'expectedIdentifier' => 'kirilicheskij-zagolovok'
            ),
            array(
                'data' => array('title' => 'Test title', 'identifier' => 'custom-identifier'),
                'expectedIdentifier' => 'custom-identifier'
            )
        );
    }
}
