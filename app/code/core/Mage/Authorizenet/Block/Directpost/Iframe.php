<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirectPost iframe block
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Block_Directpost_Iframe extends Mage_Core_Block_Template
{
    /**
     * Request params
     * @var array
     */
    protected $_params = array();

    /**
     * Internal constructor
     * Set template for iframe
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('directpost/iframe.phtml');
    }

    /**
     * Set output params
     *
     * @param array $params
     * @return Mage_Authorizenet_Block_Directpost_Iframe
     */
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
}
