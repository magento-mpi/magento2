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
class Magento_Catalog_Model_Observer_ReindexTest extends PHPUnit_Framework_TestCase
{
    /**
     * Positive test for fulltext reindex
     */
    public function testFulltextReindex()
    {
        $affectedProduct = array(1, 2, 3);

        $fulltextReindex = $this->getMock(
            'Magento_CatalogSearch_Model_Resource_Fulltext',
            array('rebuildIndex'),
            array(),
            '',
            false
        );
        $fulltextReindex->expects($this->once())
            ->method('rebuildIndex')
            ->with(
                $this->logicalOr(
                    $this->equalTo(null),
                    $this->equalTo($affectedProduct)
                )
            );

        $objectManager = $this->getMock(
            'Magento\ObjectManager\ObjectManager',
            array('get'),
            array(),
            '',
            false
        );
        $objectManager->expects($this->once())
            ->method('get')
            ->with('Magento_CatalogSearch_Model_Resource_Fulltext')
            ->will($this->returnValue($fulltextReindex));

        $observer = new \Magento\Event\Observer(
            array(
                'data_object' => new \Magento\Object(
                    array('affected_product_ids' => $affectedProduct)
                )
            )
        );

        /** @var $objectManager \Magento\ObjectManager */
        $object = new Magento_Catalog_Model_Observer_Reindex($objectManager);
        $this->assertInstanceOf('Magento_Catalog_Model_Observer_Reindex', $object->fulltextReindex($observer));
    }
}
