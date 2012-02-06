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
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API User ACL model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl extends Zend_Acl
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('admin'));

        $this->addResource(new Zend_Acl_Resource('product'));
        $this->allow('guest', 'product', array('_create', '_retrieve', '_update', '_delete'));
        $this->allow('admin', 'product', array('_create', '_retrieve', '_update', '_delete'));

        $this->addResource(new Zend_Acl_Resource('products'));
        $this->allow('guest', 'products', array('_create', '_retrieve', '_update', '_delete'));
        $this->allow('admin', 'products', array('_create', '_retrieve', '_update', '_delete'));

        $this->addResource(new Zend_Acl_Resource('customer'));
        $this->allow('guest', 'customer', array('_retrieve'));
        $this->allow('admin', 'customer', array('_create', '_retrieve', '_update', '_delete'));

        $this->addResource(new Zend_Acl_Resource('customers'));
        $this->allow('guest', 'customers', array('_retrieve'));
        $this->allow('admin', 'customers', array('_create', '_retrieve', '_update', '_delete'));

        $this->addResource(new Zend_Acl_Resource('reviews'));
        $this->allow('guest', 'reviews', array('_retrieve'));
        $this->allow('admin', 'reviews', array('_create', '_retrieve'));

        $this->addResource(new Zend_Acl_Resource('review'));
        $this->allow('guest', 'review', array('_retrieve'));
        $this->allow('admin', 'review', array('_retrieve', '_update', '_delete'));
    }

    /**
     * Save ACL data
     *
     * @return Mage_Api2_Model_Acl
     */
    public function save()
    {
        file_put_contents(dirname(__FILE__) . '/Acl/data', serialize($this));

        return $this;
    }
}
