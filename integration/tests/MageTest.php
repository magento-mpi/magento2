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
 * @magentoDataFixture _fixtures/categories.php
 */
class MageTest extends Magento_Test_Webservice
{
    public function testLogin()
    {
        $this->assertNotEmpty($this->getWebService()->login('api', 'apiapi'));
    }

    public function testProductInfo()
    {
        $category = $this->call('catalog_category.info', array(3));
        $this->assertEquals('Category 1', $category['name']);
        $this->assertEquals('1/2/3', $category['path']);
    }

    public function testProductCreate()
    {
      $categoryFixture = simplexml_load_file(__DIR__.'/_fixtures/category.xml');
      $data = self::simpleXmlToArray($categoryFixture);

      $result = $this->call('category.create', $data);

      $categoryLoaded = new Mage_Catalog_Model_Category();
      $categoryLoaded->load($result);

      $this->assertEquals($result,$categoryLoaded->getId());
      $this->assertEquals('Category 2.2', $categoryLoaded['name']);
      
    }
}
