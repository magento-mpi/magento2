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
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleOptimizer product model
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Googleoptimizer_Model_Code extends Mage_Core_Model_Abstract
{
    protected $_entity = null;
    protected $_entityType = null;
    protected $_scriptTypes = array('control', 'tracking', 'conversion');

    protected function _construct()
    {
        parent::_construct();
        $this->_init('googleoptimizer/code');
    }

    public function setEntity(Varien_Object $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function getEntityType()
    {
        return $this->_entityType;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $entity
     * @return Mage_Googleoptimizer_Model_Code
     */
    public function loadScripts($storeId)
    {
        if (is_null($this->getEntity()) || is_null($this->getEntityType())) {
            return $this;
        }

        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $this->getResource()->loadByEntityType($this, $storeId);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $entity
     * @return Mage_Googleoptimizer_Model_Code
     */
    public function saveScripts($storeId)
    {
        if (is_null($this->getEntity()) || is_null($this->getEntityType())) {
            return $this;
        }
        if (!$this->getEntity()->getGoogleOptimizerCodes()) {
            return $this;
        }

        $this->setData($this->getEntity()->getGoogleOptimizerCodes())
            ->setEntityId($this->getEntity()->getId())
            ->setEntityType($this->getEntityType())
            ->setStoreId($storeId);

        //use default scripts, need to delete scripts for current store
        if ($this->getStoreFlag()) {
            $this->deleteScripts($storeId);
            return $this;
        }
        // first saving of scripts in store different from default, need to save for default store too
//        if ($this->getStoreId() != '0' && !$this->getCodeId() && !$this->getStoreFlag()) {
//            $clone = clone $this;
//            $clone->setStoreId(0)
//                ->save();
//        }

        $this->save();
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $entity
     * @return Mage_Googleoptimizer_Model_Code
     */
    public function deleteScripts($storeId)
    {
        if (is_null($this->getEntity()) || is_null($this->getEntityType())) {
            return $this;
        }
        $this->getResource()->deleteByEntityType($this, $storeId);
        return $this;
    }
}