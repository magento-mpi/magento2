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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Filter item model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Layer_Filter_Item extends Varien_Object
{
    /**
     * Get filter instance
     * @return Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            Mage::throwException(Mage::helper('catalog')->__('Filter must be as object. Set correct filter please'));
        }
        return $filter;
    }

    public function getUrl()
    {
        $query = array($this->getFilter()->getRequestVar()=>$this->getValue());
        return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
    }

    public function getRemoveUrl()
    {
        $query = array($this->getFilter()->getRequestVar()=>$this->getFilter()->getResetValue());
        $params = $query;
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query']   = $query;
        return Mage::getUrl('*/*/*', $params);
    }

    public function getName()
    {
        return $this->getFilter()->getName();
    }
}
