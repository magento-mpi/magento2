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
 * Tag listController test
 *
 */
class Mage_Tag_Controllers_ListTest extends Mage_Tag_Controllers_AbstractTestCase
{
    /**
     * Check if tag is at list page
     *
     */
    public function testIndexAction()
    {
        ob_start();
        try {
            // dispatch controller
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('list')
                ->setActionName('index')
            ;
            Mage::app()->getFrontController()->dispatch();

            // check if list block is in layout
            $this->assertTrue(
                get_class(Mage::getSingleton('core/layout')->getBlock('tags_all'))
                ===
                Mage::getConfig()->getBlockClassName('tag/all'),
                'Block tag/all (tags_all) not found in layout'
            );

            // check if new created tag is at list page
            $contents = ob_get_clean();
            $this->assertContains($this->_tag->getName(), $contents,
                'New created tag not found at the page with all tags'
            );
        }
        catch (Exception $e) {
            ob_get_clean();
            throw $e;
        }
    }
}
