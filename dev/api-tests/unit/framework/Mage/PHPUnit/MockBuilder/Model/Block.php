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
 * Class which creates mock object for blocks when they are created
 * in a code using createBlock('...') method of layout
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Model_Block extends Mage_PHPUnit_MockBuilder_Model_Model
{
    /**
     * Delegators pool key. Needed different pool for block models.
     *
     * @var string
     */
    protected $_delegatorsPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_BLOCK_DELEGATORS;

    /**
     * Returns PHPUnit block helper.
     *
     * @return Mage_PHPUnit_Helper_Model_Block
     */
    protected function _getModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_block');
    }
}
