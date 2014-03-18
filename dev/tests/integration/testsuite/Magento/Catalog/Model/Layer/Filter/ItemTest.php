<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Item.
 */
namespace Magento\Catalog\Model\Layer\Filter;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Item
     */
    protected $_model;

    protected function setUp()
    {
        $layer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('\Magento\Catalog\Model\Layer\Category');
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Filter\Item', array(
            'data' => array(
                'filter' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Filter\Category', array('layer' => $layer)),
                'value'  => array('valuePart1', 'valuePart2'),
            )
        ));
    }

    public function testGetFilter()
    {
        $filter = $this->_model->getFilter();
        $this->assertInternalType('object', $filter);
        $this->assertSame($filter, $this->_model->getFilter());
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testGetFilterException()
    {
        /** @var $model \Magento\Catalog\Model\Layer\Filter\Item */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Layer\Filter\Item'
        );
        $model->getFilter();
    }

    /**
     * @magentoAppArea frontend
     */
    public function testGetUrl()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\App\Action\Action',
            array(
                'request' => $request,
                'response' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\TestFramework\Response'
                )
            )
        );
        $this->assertStringEndsWith('/?cat%5B0%5D=valuePart1&cat%5B1%5D=valuePart2', $this->_model->getUrl());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testGetRemoveUrl()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->create('Magento\App\RequestInterface');

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\App\RequestInterface'
        )->setRoutingInfo(
            array('requested_route' => 'x', 'requested_controller' => 'y', 'requested_action' => 'z')
        );

        $request->setParam('cat', 4);
        $this->_model->getFilter()->apply(
            $request,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\View\LayoutInterface'
            )->createBlock(
                'Magento\View\Element\Text'
            )
        );

        $this->assertStringEndsWith('/x/y/z/?cat=3', $this->_model->getRemoveUrl());
    }

    public function testGetName()
    {
        $this->assertEquals('Category', $this->_model->getName());
    }

    public function testGetValueString()
    {
        $this->assertEquals('valuePart1,valuePart2', $this->_model->getValueString());
    }
}
