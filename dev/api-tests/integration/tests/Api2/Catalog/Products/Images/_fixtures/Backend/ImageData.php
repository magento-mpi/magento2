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
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$filepath = dirname(__FILE__) . '/../product.jpg';
$fileContent = base64_encode(file_get_contents($filepath));
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
