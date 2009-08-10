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
 * @package    Mage_PackageName
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Adminhtml_Block_Media_Uploader Test Case
 */
class Mage_Core_Helper_StringTest extends Mage_TestCase
{
    /**
     *
     *
     * @dataProvider strSplitDataProvider
     * @see bug #16021
     * @group bugs
     */
    public function testStrSplit($exept, $str, $num)
    {
        $this->assertEquals($exept, Mage::helper('core/string')->str_split($str, $num));
    }



    /**
     * Provides test unit with data
     *
     * @return array test data
     */
    public function strSplitDataProvider()
    {
        return array(
            array(array('50-P30-FL-SN-12', '0'), '50-P30-FL-SN-120', 15),
            array(array('50-P30-FL-SN-1', '20'), '50-P30-FL-SN-120', 14),
            array(array('50-P30-FL-SN-1', '2 '), '50-P30-FL-SN-12 ', 14),
            array(array('50-P30-FL-SN-12', ' '), '50-P30-FL-SN-12 ', 15),
            array(array('50-P30-FL-SN-1'), '50-P30-FL-SN-1', 14),
        );
    }
}
