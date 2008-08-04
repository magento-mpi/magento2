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
 * @package    Mage_PackageName
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Core_Controller_Varien_Action extending
 *
 * @name       name
 * @author	   Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Controller_Varien_ActionChild extends Mage_Core_Controller_Varien_Action
{
    public function __construct()
    {
        parent::__construct(new Zend_Controller_Request_Http(), new Zend_Controller_Response_Http());
    }

    public function isUrlInternal ($url)
    {
        return $this->_isUrlInternal($url);
    }
}

/**
 * Mage_Core_Controller_Varien_Action Test Case
 *
 * @name       name
 * @author	   Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Controller_Varien_ActionTest extends PHPUnit_Framework_TestCase
{
    /**
     *  tests whether URL is internal
     */
    public function testIsUrlInternal ()
    {
        $checkUrls = array(
            'http://somedomain.com/path/to/nowhere',
            Mage::app()->getStore()->getBaseUrl() . uniqid(),
            Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true) . uniqid()
        );
        $object = new Mage_Core_Controller_Varien_ActionChild();
        return $this->assertTrue(
            !$object->isUrlInternal($checkUrls[0])
            && $object->isUrlInternal($checkUrls[1])
            && $object->isUrlInternal($checkUrls[2])
        );
    }

}