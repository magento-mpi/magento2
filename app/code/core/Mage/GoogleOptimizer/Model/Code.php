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
    const DEFAULT_ENTITY_TYPE = 'product';

    protected $_entityType = null;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('googleoptimizer/code');
        if (is_null($this->_entityType)) {
            $this->_entityType = self::DEFAULT_ENTITY_TYPE;
        }
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
    public function loadCodes(Varien_Object $entity)
    {
        $this->getResource()->loadByEntityType($this, $entity);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $entity
     * @return Mage_Googleoptimizer_Model_Code
     */
    public function saveCodes(Varien_Object $entity)
    {
        $this->setData($entity->getGoogleOptimizerCodes())
            ->setEntityId($entity->getId())
            ->setEntityType($this->getEntityType())
            ->setStoreId($entity->getStoreId());

//        if ($entity->getStoreId() != '0' && !$this->getCodeId()) {
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
    public function deleteCodes(Varien_Object $entity)
    {
        $this->getResource()->deleteByEntityType($this, $entity);
        return $this;
    }
}