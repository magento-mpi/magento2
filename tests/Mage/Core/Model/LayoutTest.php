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
 * @package    Tests_Mage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @category   default
 * @package    Tests_Mage
 */
class Mage_Core_Model_LayoutTest extends PHPUnit_Framework_TestCase
{
    protected $_layout;
    
    public function __construct() {
        $this->_layout = Mage::getSingleton('core', 'layout');
    }
    
    public function setUp() {
    }
    
    public function tearDown() {
    }
    
    public function testCreateBlock()
    {
        // empty name
        $block1 = $this->_layout->createBlock('tpl');
        $this->assertType('object', $block1);
        
        // with name
        $block2 = $this->_layout->createBlock('tpl', 'test_block');
        $this->assertType('object', $block2);
        $this->assertEquals('test_block', $block2->getName());
        
        // with attributes
        $block3 = $this->_layout->createBlock('tpl', 'test_block1', array('testAttr'=>'test  '));
        $this->assertType('object', $block3);
    }
    
    public function testGetBlock()
    {
        $block = $this->_layout->getBlock('test_block');
        $this->assertType('object', $block);
    }
    
    public function testAllBlockOperation()
    {
        $this->_layout->createBlock('tpl', 'tmp_block');
        $this->_layout->removeBlock('tmp_block');
        $block = $this->_layout->getBlock('tmp_block');
        
        var_dump($block);
    }
}