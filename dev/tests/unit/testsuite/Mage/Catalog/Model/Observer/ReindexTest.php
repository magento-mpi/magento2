<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Catalog Observer Reindex
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Observer_ReindexTest extends PHPUnit_Framework_TestCase
{
    /**
     * Positive test for fulltext reindex
     */
    public function testFulltextReindex()
    {
        $affectedProduct = array(1, 2, 3);

        $fulltextReindex = $this->getMock(
            'Mage_CatalogSearch_Model_Resource_Fulltext',
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
            'Magento_ObjectManager_ObjectManager',
            array('get'),
            array(),
            '',
            false
        );
        $objectManager->expects($this->once())
            ->method('get')
            ->with('Mage_CatalogSearch_Model_Resource_Fulltext')
            ->will($this->returnValue($fulltextReindex));

        $observer = new Varien_Event_Observer(
            array(
                'data_object' => new Varien_Object(
                    array('affected_product_ids' => $affectedProduct)
                )
            )
        );

        /** @var $objectManager Magento_ObjectManager */
        $object = new Mage_Catalog_Model_Observer_Reindex($objectManager);
        $this->assertInstanceOf('Mage_Catalog_Model_Observer_Reindex', $object->fulltextReindex($observer));
    }
}
