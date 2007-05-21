<?php

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