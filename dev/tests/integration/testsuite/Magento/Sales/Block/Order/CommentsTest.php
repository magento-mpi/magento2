<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Order;

class CommentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Block\Order\Comments
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Sales\Block\Order\Comments');
    }

    /**
     * @param string $commentedEntity
     * @param string $expectedClass
     * @dataProvider getCommentsDataProvider
     */
    public function testGetComments($commentedEntity, $expectedClass)
    {
        $commentedEntity = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($commentedEntity);
        $this->_block->setEntity($commentedEntity);
        $comments = $this->_block->getComments();
        $this->assertInstanceOf($expectedClass, $comments);
    }

    /**
     * @return array
     */
    public function getCommentsDataProvider()
    {
        return array(
            array(
                'Magento\Sales\Model\Order\Invoice',
                'Magento\Sales\Model\Resource\Order\Invoice\Comment\Collection'
            ),
            array(
                'Magento\Sales\Model\Order\Creditmemo',
                'Magento\Sales\Model\Resource\Order\Creditmemo\Comment\Collection'
            ),
            array(
                'Magento\Sales\Model\Order\Shipment',
                'Magento\Sales\Model\Resource\Order\Shipment\Comment\Collection'
            )
        );
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testGetCommentsWrongEntityException()
    {
        $entity = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $this->_block->setEntity($entity);
        $this->_block->getComments();
    }
}
