<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

class WebService_Catalog_Product_AttributeTest extends WebService_TestCase_Abstract
{
    /**
     * tests catalog_product_attribute.currentStore
     *
     * @dataProvider connectorProvider
     */
    public function testCurrentStore (WebService_Connector_Interface $connector)
    {
        $storeIds = array();
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            $storeIds[] = $store->getId();
        }
        $rndStoreId = $storeIds[mt_rand(0, count($storeIds) - 1)];
        $result = $connector->call('catalog_product_attribute.currentStore', array($rndStoreId));
        $this->assertEquals($result, $rndStoreId);
    }

    /**
     * tests catalog_product_attribute.list
     *
     * @dataProvider connectorProvider
     */
    public function testList (WebService_Connector_Interface $connector)
    {
        $this->_createAttribute($connector);

        $expected = array(
            'attribute_id' => $this->_attribute->getId(),
            'code'         => $this->_attribute->getAttributeCode(),
            'type'         => $this->_attribute->getFrontendInput(),
            'required'     => (int)$this->_attribute->getIsRequired(),
            'scope'        => 'store'
        );

        $attributes = $connector->call(
            'catalog_product_attribute.list',
            array($this->_attribute->getAttributeSetId())
        );
        $this->_deleteAttribute();
        $this->assertTrue(in_array($expected, $attributes));
    }

    /**
     * tests catalog_product_attribute.options
     *
     * @dataProvider connectorProvider
     */
    public function testOptions (WebService_Connector_Interface $connector)
    {
        $this->_createAttribute($connector);

        $attrOptions = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($this->_attribute->getId())
            ->setStoreFilter()
            ->load()
            ->toOptionArray();

        $options = $connector->call(
            'catalog_product_attribute.options',
            array($this->_attribute->getId())
        );

        $this->_deleteAttribute();

        $c = count($attrOptions);
        for ($i = 0; $i < $c; $i++) {
            $this->assertTrue(in_array($attrOptions[$i], $options));
        }
    }

    /**
     *  Creates temporary attribute
     */
    protected function _createAttribute (WebService_Connector_Interface $connector)
    {
        $attributeSets = $connector->call('product_attribute_set.list');
        $set = current($attributeSets);

        $groups = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($set['set_id'])
            ->load();
        foreach ($groups as $group) {
            break;
        }

        $data = array(
            'attribute_code' => uniqid(),
            'is_global' => 0,
            'frontend_input' => 'select',
            'option' => array(
                'value' => array(
                    'option_0' => array(0 => uniqid()),
                    'option_1' => array(0 => uniqid())
                )
            )
        );

        /* @var $attribute Mage_Catalog_Model_Entity_Attribute */
        $attribute = Mage::getModel('catalog/entity_attribute')
            ->addData($data)
            ->setEntityTypeId( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() )
            ->setIsUserDefined(1)
            ->setAttributeSetId($set['set_id'])
            ->setAttributeGroupId($group->getId())
            ->save();

        $this->_attribute = $attribute;
    }

    /**
     *  Destroy temporary attribute
     *
     *  @param    none
     *  @return	  void
     */
    protected function _deleteAttribute ()
    {
        if (isset($this->_attribute) && $this->_attribute instanceof Mage_Catalog_Model_Entity_Attribute) {
            $this->_attribute->delete();
        }
    }
}