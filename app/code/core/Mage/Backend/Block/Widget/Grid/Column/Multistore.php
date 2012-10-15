<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column block that is displayed only in multistore mode
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Multistore extends Mage_Backend_Block_Widget_Grid_Column
{
    /**
     * Application
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_app = isset($data['app']) ? $data['app'] : Mage::app();
        parent::__construct($data);
    }

    /**
     * Get header css class name
     *
     * @return string
     */
    public function isDisplayed()
    {
        return !$this->_app->isSingleStoreMode();
    }
}
