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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

/**
 * Tag customerController test
 *
 */
class Mage_Tag_Controllers_CustomerTest extends Mage_Tag_Controllers_AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($this->_customer);
    }

    /**
     * Check if added product tag is at the customer tags list page
     *
     */
    public function testIndexAction()
    {
        ob_start();
        try {
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('customer')
                ->setActionName('index');
            Mage::app()->getFrontController()->dispatch();

            $this->assertThat(
                Mage::getSingleton('core/layout')->getBlock('customer_tags'),
                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/customer_tags'))
            );
            $contents = ob_get_clean();
            $this->assertContains($this->_tag->getName(), $contents);
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }

    public function testViewAction()
    {
        ob_start();
        try {
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('customer')
                ->setActionName('view')
                ->setParam('tagId', $this->_tag->getId());
            Mage::app()->getFrontController()->dispatch();

            $this->assertThat(
                Mage::getSingleton('core/layout')->getBlock('customer_view'),
                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/customer_view'))
            );
            $contents = ob_get_clean();
            $this->assertContains($this->_product->getName(), $contents);
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }

    public function testEditAction()
    {
        ob_start();
        try {
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('customer')
                ->setActionName('edit')
                ->setParam('tagId', $this->_tag->getId());
            Mage::app()->getFrontController()->dispatch();

            $this->assertThat(
                Mage::getSingleton('core/layout')->getBlock('customer_edit'),
                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/customer_edit'))
            );
            $contents = ob_get_clean();
            $this->assertContains($this->_tag->getName(), $contents);
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }

    public function testRemoveAction()
    {
        ob_start();
        try {
            $_oldTagId = $this->_tag->getId();
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('customer')
                ->setActionName('remove')
                ->setParam('tagId', $this->_tag->getId());
            Mage::app()->getFrontController()->dispatch();

            $contents = ob_get_clean();
            $this->_tag->addSummary(Mage::app()->getStore()->getId());
            $this->assertNull($this->_tag->getCustomers());
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }

    public function testSaveAction()
    {
        ob_start();
        try {
            $newTagName = 'new_'.$this->_tag->getName();
            $_POST['tagName'] = $newTagName;
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('customer')
                ->setActionName('save')
                ->setParam('tagId', $this->_tag->getId());
            Mage::app()->getFrontController()->dispatch();

            $contents = ob_get_clean();

            $this->_tag = Mage::getModel('tag/tag')->loadByName($newTagName);
            $this->assertGreaterThan(0, $this->_tag->getId());
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }

    protected function tearDown()
    {
        if ($this->_customer) {
            Mage::getSingleton('customer/session')->logout();
        }
        parent::tearDown();
    }
}
