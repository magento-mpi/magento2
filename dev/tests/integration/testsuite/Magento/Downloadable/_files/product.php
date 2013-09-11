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

$product = Mage::getModel('\Magento\Catalog\Model\Product');
$product->setTypeId(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Downloadable Product')
    ->setSku('downloadable-product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
    ->setDownloadableData(array(
        'link' => array(
            array(
                'title'        => 'Downloadable Product Link',
                'type'         => \Magento\Downloadable\Helper\Download::LINK_TYPE_URL,
                'is_shareable' => \Magento\Downloadable\Model\Link::LINK_SHAREABLE_CONFIG,
                'link_url'     => 'http://example.com/downloadable.txt',
                'link_id'      => 0,
                'is_delete'    => null,
            ),
        ),
    ))
    ->save()
;
