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
 * @category   default
 * @package    Tests_MageTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


require_once('Mage.php');

class MageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Registry test
     */
    public function testRegistry()
    {
        Mage::register('var', 'value');
        $this->assertEquals('value', Mage::registry('var'));
    }
    
    public function testConfig()
    {
        $config = Mage::getConfig();
        $this->assertTrue($config->getNode('global') instanceof SimpleXMLElement);
        $this->assertTrue($config->getNode('modules') instanceof SimpleXMLElement);
        $this->assertTrue($config->getNode('admin') instanceof SimpleXMLElement);
        $this->assertTrue($config->getNode('front') instanceof SimpleXMLElement);
    }
    
    public function testInit()
    {
        $this->assertNotNull(Mage::registry('events'));
        $this->assertNotNull(Mage::registry('config'));
        $this->assertNotNull(Mage::registry('resources'));
        $this->assertNotNull(Mage::registry('session'));
        $this->assertNotNull(Mage::registry('website'));
    }
    
    public function testCreateBlock()
    {
        if (is_null(Mage::registry('controller'))) {
            
        }
        //$block = Mage::createBlock('tpl', 'testBlock');
    }
    
    public function testGetBlock()
    {
        
    }
    
    public function testGetModel()
    {
        $layoutModel = Mage::getModel('core', 'layout');
        $this->assertType('object', $layoutModel);
    }
    
    public function getSingleton()
    {
        
    }
}