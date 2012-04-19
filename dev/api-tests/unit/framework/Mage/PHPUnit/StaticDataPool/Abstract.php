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
 * Abstract class for pools of data.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_StaticDataPool_Abstract
{
    /**
     * Method is called from pool container to do additional actions
     * before clean pool's data from pool container.
     */
    public function beforeClean()
    {
    }
}
