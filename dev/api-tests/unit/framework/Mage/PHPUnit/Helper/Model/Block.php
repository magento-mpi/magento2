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
 * Unit testing helper for blocks.
 * Is a singleton.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Model_Block extends Mage_PHPUnit_Helper_Model_Model
{
    /**
     * Name of the pool with block's real class names
     *
     * @var string
     */
    protected $_realModelClassesPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_REAL_BLOCK_CLASSES;

    /**
     * Group type name
     *
     * @var string
     */
    protected $_group = 'block';
}
