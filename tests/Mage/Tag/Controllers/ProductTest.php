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
    $suite = new PHPUnit_Framework_TestSuite();
    $suite->addTestSuite('Mage_Tag_Controllers_ProductTest');
    PHPUnit_TextUI_TestRunner::run($suite);
    exit;
}

/**
 * Tag productController test
 *
 */
class Mage_Tag_Controllers_ProductTest extends Mage_Tag_Controllers_AbstractTestCase
{
    /**
     * Check if added product tag is at the page
     *
     */
    public function testListAction()
    {
        ob_start();
        try {
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('product')
                ->setActionName('list')
                ->setParam('tagId', $this->_tag->getId());
            Mage::app()->getFrontController()->dispatch();

            $this->assertThat(
                Mage::getSingleton('core/layout')->getBlock('tag_products'),
                new PHPUnit_Framework_Constraint_IsInstanceOf(Mage::getConfig()->getBlockClassName('tag/product_result'))
            );
            $contents = ob_get_clean();
            $this->assertContains($this->_tag->getName(), $contents);
            $this->assertContains($this->_product->getName(), $contents);
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }
}
