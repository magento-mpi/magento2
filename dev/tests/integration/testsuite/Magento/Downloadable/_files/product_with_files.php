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

$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
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
        'link' => array(array(
            'link_id'        => 0,
            'product_id'     => 1,
            'sort_order'     => '0',
            'title'          => 'Downloadable Product Link',
            'sample'         => array(
                'type'       => Magento_Downloadable_Helper_Download::LINK_TYPE_FILE,
                'url'        => null,
                'file'       => json_encode(array(array(
                    'file'   => '/n/d/jellyfish_1_3.jpg',
                    'name'   => 'jellyfish_1_3.jpg',
                    'size'   => 54565,
                    'status' => 0,
                ))),
            ),
            'file'       => json_encode(array(array(
                'file'   => '/j/e/jellyfish_2_4.jpg',
                'name'   => 'jellyfish_2_4.jpg',
                'size'   => 56644,
                'status' => 0,
            ))),
            'type'                => Magento_Downloadable_Helper_Download::LINK_TYPE_FILE,
            'is_shareable'        => Magento_Downloadable_Model_Link::LINK_SHAREABLE_CONFIG,
            'link_url'            => null,
            'is_delete'           => 0,
            'number_of_downloads' => 15,
            'price'               => 15.00,
        )),
        'sample'  => array(array(
            'is_delete'  => 0,
            'sample_id'  => 0,
            'title'      => 'Downloadable Product Sample Title',
            'type'       => Magento_Downloadable_Helper_Download::LINK_TYPE_FILE,
            'file'       => json_encode(array(array(
                'file'   => '/f/u/jellyfish_1_4.jpg',
                'name'   => 'jellyfish_1_4.jpg',
                'size'   => 1024,
                'status' => 0,
            ))),
            'sample_url' => null,
            'sort_order' => '0',
        ))
    ))
    ->save();
