<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme files model class
 */
class Mage_Core_Model_Theme_Files extends Mage_Core_Model_Abstract
{
    /**
     * css file type
     */
    const TYPE_CSS = 'css';

    /**
     * Theme files model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme_Files');
    }
}
