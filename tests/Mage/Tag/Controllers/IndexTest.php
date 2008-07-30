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
    $suite->addTestSuite('Mage_Tag_Controllers_IndexTest');
    PHPUnit_TextUI_TestRunner::run($suite);
    exit;
}

/**
 * Tag indexController test
 *
 */
class Mage_Tag_Controllers_IndexTest extends Mage_Tag_Controllers_AbstractTestCase
{
    /**
     * Check if tag is added by saveAction
     *
     */
    public function testSaveAction()
    {
        ob_start();
        try {
            // create unique tag name
            for ($i = 0; ; $i++) {
                $tagName = uniqid();
                $tag = Mage::getModel('tag/tag')
                    ->loadByName($tagName);
                if (0 == $tag->getId()) {
                    break;
                }
                if ($i > 10) {
                    $this->fail('Failed to generate unique random tag.');
                }
            }
            // create a success url
            $successUrl = uniqid();
            // log in customer
            $session = Mage::getSingleton('customer/session')
                ->setCustomerAsLoggedIn($this->_customer);

            // dispatch controller
            $_GET['tagName'] = $tagName;
            Mage::app()->getFrontController()->getRequest()
                ->setModuleName('tag')
                ->setControllerName('index')
                ->setActionName('save')
                ->setParam('product', $this->_product->getId())
                ->setParam(Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED, $successUrl)
            ;
            Mage::app()->getFrontController()->dispatch();

            // check if our new tag has been saved
            $tag = Mage::getModel('tag/tag')
                ->loadByName($tagName);
            $this->assertTrue(0 != $tag->getId(), sprintf('Failed to save tag "%s".', $tagName));

            // check if our new tag has pending status
            $this->assertTrue($tag->getPendingStatus() == $tag->getStatus(), 'New tag has wrong status. Expected pending.');

            // check if our new tag is related to product/customer/store
            $relation = Mage::getModel('tag/tag_relation')->loadByTagCustomer(
                $this->_product->getId(), $tag->getId(), $this->_customer->getId(), $this->_customer->getStoreId()
            );
            $this->assertTrue(0 != $relation->getId(), 'Tag saved, but relation is not.');
            $this->assertTrue(
                (0 != $relation->getTagId()) && (0 != $relation->getCustomerId()) && (0 != $relation->getProductId()),
                'Tag saved, but relation is corrupt. Expected aggregated relation.'
            );

            // dispose of tag
            $tag->delete();
            // logoff customer
            $session->logout();

            $contents = ob_get_clean();
        }
        catch (Exception $e) {
            if ($tag && $tag->getId()) {
                $tag->delete();
            }
            if ($session && $session->isLoggedIn()) {
                $session->logout();
            }
            ob_get_clean();
            throw $e;
        }
    }
}
