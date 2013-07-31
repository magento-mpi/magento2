<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid row url generator
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Grid_Row_UrlGenerator extends Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_AuthorizationInterface $authorization
     * @param array $args
     */
    public function __construct(Magento_AuthorizationInterface $authorization, array $args = array())
    {
        $this->_authorization = $authorization;
        parent::__construct($args);

    }

    /**
     * Generate row url
     * @param Magento_Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if ($this->_authorization->isAllowed('Mage_Sales::actions_view')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
