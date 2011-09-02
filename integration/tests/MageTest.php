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
//
//    public function testLogin()
//    {
//        $this->assertNotEmpty($this->getWebService(SOAP)->login('api', 'apiapi'), 'SOAP');
//        $this->assertNotEmpty($this->getWebService(SOAPV2)->login('api', 'apiapi'), 'SOAPV2');
//        $this->assertNotEmpty($this->getWebService(XMLRPC)->login('api', 'apiapi'), 'XMLPRC');
//
//    }

    public function testProductInfo()
    {
      $categories = $this->call('catalog_category.info', array(3));

      foreach($categories as $server => $result)
      {
        $this->assertEquals('Category 12', $result['name'], $server);
        $this->assertEquals('1/2/3', $result['path'], $server);
      }
        
    }
}
