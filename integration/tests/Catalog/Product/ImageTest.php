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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API work with product images
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Catalog_Product_ImageTest extends Magento_Test_Webservice
{
    /**
     * Tests image for product creation
     *
     * @return void
     */
    public function testCreateInvalidImage()
    {
        $this->markTestIncomplete();

        $validJpg      = dirname(__FILE__) . '/_fixtures/files/images/test.jpg.jpg';
        $bmpWithJpgExt = dirname(__FILE__) . '/_fixtures/files/images/test.bmp.jpg';
        $phpWithJpgExt = dirname(__FILE__) . '/_fixtures/files/images/test.php.jpg';
        $pngWithJpgExt = dirname(__FILE__) . '/_fixtures/files/images/test.png.jpg';

        $fileData = array(
            'label'    => 'My Product Image',
            'position' => 2,
            'types'    => array('small_image', 'image', 'thumbnail'),
            'exclude'  => 0,
            'remove'   => 0,
            'file'     => array(
                'name'    => 'my_image_file',
                'content' => base64_encode(file_get_contents($validJpg)),
                'mime'    => 'image/jpeg'
            )
        );

        $result = $this->getWebService()->call('product_attribute_media.create', array('simple1', $fileData));

        $this->assertInternalType('string', $result, 'String type response expected but not received');
    }
}
