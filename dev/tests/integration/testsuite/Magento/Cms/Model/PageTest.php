<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model;

/**
 * @magentoAppArea adminhtml
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $model;

    protected function setUp()
    {
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\User\Model\User'
        )->loadByUsername(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME
        );

        /** @var $session \Magento\Backend\Model\Auth\Session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Backend\Model\Auth\Session'
        );
        $session->setUser($user);
    }

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
            array('data' => array('title' => 'Test title', 'stores' => [1]), 'expectedIdentifier' => 'test-title'),
            array(
                'data' => array('title' => 'Кирилический заголовок', 'stores' => [1]),
                'expectedIdentifier' => 'kirilicheskij-zagolovok'
            ),
            array(
                'data' => array('title' => 'Test title', 'identifier' => 'custom-identifier', 'stores' => [1]),
                'expectedIdentifier' => 'custom-identifier'
            )
        );
    }
}
