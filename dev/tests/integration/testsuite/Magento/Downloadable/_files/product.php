<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->setTypeId(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Downloadable Product')
    ->setSku('downloadable-product')
    ->setPrice(10)
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setDownloadableData(array(
        'link' => array(
            array(
                'title'        => 'Downloadable Product Link',
                'type'         => Magento_Downloadable_Helper_Download::LINK_TYPE_URL,
                'is_shareable' => Magento_Downloadable_Model_Link::LINK_SHAREABLE_CONFIG,
                'link_url'     => 'http://example.com/downloadable.txt',
                'link_id'      => 0,
                'is_delete'    => null,
            ),
        ),
    ))
    ->save()
;
