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
 * Abstract configuration class
 *
 * Used to retrieve core configuration values
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Model_Config_Base extends Varien_Simplexml_Config
{
    /**
     * Constructor
     *
     */
    public function __construct($sourceData = null)
    {
        $this->_elementClass = 'Mage_Core_Model_Config_Element';
        parent::__construct($sourceData);
    }
}
