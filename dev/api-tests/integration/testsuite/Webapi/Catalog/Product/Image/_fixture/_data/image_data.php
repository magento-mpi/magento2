<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$filePath = TEST_FIXTURE_DIR . '/_data/Catalog/Product/product.jpg';
$fileContent = base64_encode(file_get_contents($filePath));
return array(
    'full_create' => array(
        'file_name' => 'product_image' . uniqid(),
        'file_content' => $fileContent,
        'file_mime_type' => 'image/jpeg',
        'label'    => 'test product image ' . uniqid(),
        'position' => 2,
        'types'    => array('small_image'),
        'exclude'  => 0
    ),
    'create_with_empty_file_content' => array(
        'file_name' => 'product_image' . uniqid(),
        'file_content' => '',
        'file_mime_type' => 'image/jpeg',
        'label'    => 'test product image ' . uniqid(),
        'position' => 2,
        'types'    => array('small_image'),
        'exclude'  => 0
    ),
    'create_with_invalid_image' => array(
        'file_name' => 'product_image' . uniqid(),
        'file_content' => 'abdbavvghjkdgvfgsydauvsdcfbgdsy321635bhdsisat67832b32y7r82vrdsw==',
        'file_mime_type' => 'image/jpeg',
        'label'    => 'test product image ' . uniqid(),
        'position' => 2,
        'types'    => array('small_image'),
        'exclude'  => 0
    ),
    'create_with_invalid_base64' => array(
        'file_name' => 'product_image' . uniqid(),
        'file_content' => 'вкегмсыпв/*-+!"№;№"%;*#@$#$%^^&fghjf fghftyu vftib',
        'file_mime_type' => 'image/jpeg',
        'label'    => 'test product image ' . uniqid(),
        'position' => 2,
        'types'    => array('small_image'),
        'exclude'  => 0
    ),
    'create_with_invalid_mime' => array(
        'file_name' => 'product_image' . uniqid(),
        'file_content' => $fileContent,
        'file_mime_type' => 'plain/text',
        'label'    => 'test product image ' . uniqid(),
        'position' => 2,
        'types'    => array('small_image'),
        'exclude'  => 0
    ),
    'create_with_empty_types' => array(
        'file_name' => 'product_image' . uniqid(),
        'file_content' => $fileContent,
        'file_mime_type' => 'plain/text',
        'label'    => 'test product image ' . uniqid(),
        'position' => 2,
        'types'    => '',
        'exclude'  => 0
    ),
    'data_set_1' => array(
        'label' => 'test product image 1 ' . uniqid(),
        'position' => 10,
        'types'    => array('image'),
        'exclude'  => 0
    ),
    'data_set_2' => array(
        'label' => 'test product image 2 ' . uniqid(),
        'position' => 20,
        'types'    => array('small_image'),
        'exclude'  => 0
    ),
    'data_set_3' => array(
        'label' => 'test product image 3 ' . uniqid(),
        'position' => 30,
        'types'    => array('thumbnail'),
        'exclude'  => 0
    )
);
