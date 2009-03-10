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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml Catalog Product List Sortable allowed sortable attributes source
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Logging_Model_System_Config_LifetimeList
{
    /**
     * Retrieve option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $config = Mage::getConfig()->getNode('adminhtml/enterprise/logging/lifetime');
        $children = $config->children();
        foreach($children as $lt) {
            $options[] = array(
                'label' => Mage::helper('enterprise_logging')->__((string)$lt->label),
                'value' => (string)$lt->value,
            );
        }
        return $options;
    }
}