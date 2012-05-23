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
 * Abstract class for all Unit testing helpers.
 * Should be a singleton
 */
abstract class Mage_PHPUnit_Helper_Abstract
{
    /**
     * Returns Pool Container object.
     *
     * @return Mage_PHPUnit_StaticDataPoolContainer
     */
    public function getStaticDataPoolContainer()
    {
        return Mage_PHPUnit_StaticDataPoolContainer::getInstance();
    }

    /**
     * Returns object with static data from data pool container by its key.
     *
     * @param string $poolKey
     * @return Mage_PHPUnit_StaticDataPool_Abstract
     */
    protected function _getStaticDataObject($poolKey)
    {
        return $this->getStaticDataPoolContainer()->getDataObject($poolKey);
    }
}
