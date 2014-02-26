<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
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
            array(
                'data' => ['title' => 'Test title'],
                'expectedIdentifier' => 'test-title'
            ),
            array(
                'data' => ['title' => 'Кирилический заголовок'],
                'expectedIdentifier' => 'kirilicheskij-zagolovok'
            ),
            array(
                'data' => ['title' => 'Test title', 'identifier' => 'custom-identifier'],
                'expectedIdentifier' => 'custom-identifier'
            ),
        );
    }
}
