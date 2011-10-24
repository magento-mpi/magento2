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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Buffer Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CmsWidgets_BufferTest extends Mage_Selenium_TestCase
{



    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * @test
     */
    public function buff()
    {
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget('catalog_product_link_widget');
//        $search = array('filter_widget_id' => '10', 'filter_title' => 'test');
//        $this->cmsWidgetsHelper()->deleteWidget($search);

        //You can use https://kd.varien.com/dev/artem.shugayev/16/index.php/admin/  admin/123123q for testing
    }
}
