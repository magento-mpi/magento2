<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for pool of real resource model names
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_StaticDataPool_ResourceModelName extends Mage_PHPUnit_StaticDataPool_Abstract
{
    /**
     * Real resource models names.
     *  array('catalog/product' => 'mysql4_catalog/product')
     *
     * @var array
     */
    protected $_realResourceModels = array();


    /**
     * Returns real model's class name.
     *
     * @param string $model
     * @return string
     */
    public function getResourceModelName($model)
    {
        if (!isset($this->_realResourceModels[$model])) {
            return false;
        }
        return $this->_realResourceModels[$model];
    }

    /**
     * Set real resource model's name.
     *
     * @param string $model
     * @param string $realName
     */
    public function setResourceModelName($model, $realName)
    {
        $this->_realResourceModels[$model] = $realName;
    }
}
