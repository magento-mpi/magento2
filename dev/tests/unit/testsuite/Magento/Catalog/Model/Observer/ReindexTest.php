<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Catalog Observer Reindex
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Observer;

class ReindexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Positive test for fulltext reindex
     */
    public function testFulltextReindex()
    {
        $affectedProduct = array(1, 2, 3);

        $fulltextReindex = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\Fulltext',
            array('rebuildIndex', '__wakeup'),
            array(),
            '',
            false
        );
        $fulltextReindex->expects(
            $this->once()
        )->method(
            'rebuildIndex'
        )->with(
            $this->logicalOr($this->equalTo(null), $this->equalTo($affectedProduct))
        );

        $objectManager = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', array('get'), array(), '', false);
        $objectManager->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'Magento\CatalogSearch\Model\Resource\Fulltext'
        )->will(
            $this->returnValue($fulltextReindex)
        );

        $observer = new \Magento\Framework\Event\Observer(
            array('data_object' => new \Magento\Object(array('affected_product_ids' => $affectedProduct)))
        );

        /** @var $objectManager \Magento\Framework\ObjectManager */
        $object = new \Magento\Catalog\Model\Observer\Reindex($objectManager);
        $this->assertInstanceOf('Magento\Catalog\Model\Observer\Reindex', $object->fulltextReindex($observer));
    }
}
