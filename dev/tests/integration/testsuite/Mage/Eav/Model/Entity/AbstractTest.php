<?php
/**
 * Test abstract EAV entity resource model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Eav_Model_Entity_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Product is used to test fieldset-based filtration on EAV entity.
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testLoadWithEmptyFieldset()
    {
        $fixtureProductId = 1;
        $productWithFieldset = Mage::getModel('Mage_Catalog_Model_Product');
        $productWithFieldset->setFieldset(array())->load($fixtureProductId);

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($fixtureProductId);

        $this->assertEquals(
            $product->getData(),
            $productWithFieldset->getData(),
            "Empty fieldset must not influence on entity set of fields returned."
        );
    }

    /**
     * Product is used to test fieldset-based filtration on EAV entity.
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @dataProvider providerForTestLoadWithFieldset
     */
    public function testLoadWithFieldset($fieldset, $expectedFields, $fieldsetName)
    {
        $fixtureProductId = 1;
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->setFieldset($fieldset)->load($fixtureProductId);
        $actualData = $product->getData();
        /** Some data is loaded in _afterLoad and thus fieldset should not influence on it. */
        $afterloadFields = array(
            'stock_item',
            'is_in_stock',
            'is_salable',
            'category_ids',
            'group_price',
            'group_price_changed',
            'media_gallery',
            'tier_price_changed',
        );
        foreach ($afterloadFields as $afterloadField) {
            unset($actualData[$afterloadField]);
        }
        $actualFields = array_keys($actualData);
        sort($expectedFields);
        sort($actualFields);
        $this->assertEquals(
            $expectedFields,
            $actualFields,
            "Fieldset was not applied correctly during EAV entity loading in the following case: $fieldsetName"
        );
    }

    public function providerForTestLoadWithFieldset()
    {
        return array(
            array(
                array('entity_id', 'sku', 'updated_at'),
                array('entity_id', 'sku', 'updated_at'),
                "Static fields only.",
            ),
            array(
                array('special_from_date', 'weight', 'price', 'status', 'description', 'name'),
                /** Id field is always loaded. */
                array('special_from_date', 'weight', 'price', 'status', 'description', 'name', 'entity_id'),
                "EAV fields only (5 different backend types).",
            ),
            array(
                array('news_from_date', 'entity_id', 'sku', 'price', 'weight', 'status', 'description', 'name'),
                array('news_from_date', 'entity_id', 'sku', 'price', 'weight', 'status', 'description', 'name'),
                "EAV and Static fields.",
            ),
            array(
                array('custom_design_from', 'entity_id', 'sku', 'price', 'status', 'description', 'name', 'invalid'),
                array('custom_design_from', 'entity_id', 'sku', 'price', 'status', 'description', 'name'),
                "Valid EAV and Static fields plus not existing fields.",
            ),
            array(
                array('invalid'),
                /** Id field is always loaded. */
                array('entity_id'),
                "Not existing fields only.",
            ),
            array(
                array('entity_id', 'sku', 'invalid'),
                array('entity_id', 'sku'),
                "Valid static field and not existing field.",
            ),
            array(
                array('status', 'invalid'),
                /** Id field name is always loaded. */
                array('entity_id', 'status'),
                "Valid EAV field and not existing field.",
            ),
        );
    }
}
