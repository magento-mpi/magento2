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
 * @category   Tests
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Template filter model test case
 *
 */
class Mage_Cms_Model_PageTest extends Mage_TestCase
{
    /**
     * Test of block model init
     *
     * @group CmsBlock
     */
    public function testCmsModelPageLoad()
    {
        $mock = $this->_getMockModel('cms/page', array('noRoutePage'));
        $mock->expects($this->once())
            ->method('noRoutePage');
        $mock->load(null);
    }

    public function setUp()
    {
        parent::setUp();

        $this->_getDbAdapter()->begin();
        $this->_getDbAdapter()->loadFixture('cms');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->_getDbAdapter()->rollback();
    }

    public function testCheckIdentifier()
    {
        $pageFixtureRow = $this->_getDbAdapter()->getTableRow('cms_page', 1);
        $pageStoreFixtureRow = $this->_getDbAdapter()
            ->getTableRow('cms_page_store', 7);

        $page = Mage::getModel('cms/page');
        $res  = $page->checkIdentifier($pageFixtureRow->getIdentifier(),
            $pageStoreFixtureRow->getStoreId());

        $this->assertEquals($pageFixtureRow->getPageId(), $res,
            'Invalid Check Identifier for valid page and store id');

        $res  = $page->checkIdentifier('-', 0);
        $this->assertFalse($res);
    }

    public function testNoRoute()
    {

    }
}


/* EoF Mage_Cms_Model_PageTest.php */