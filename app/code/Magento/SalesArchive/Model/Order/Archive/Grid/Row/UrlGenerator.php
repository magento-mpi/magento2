<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales Archive Grid row url generator
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_SalesArchive_Model_Order_Archive_Grid_Row_UrlGenerator
    extends Magento_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * @var $_authorizationModel Magento_AuthorizationInterface
     */
    protected $_authorizationModel;

    /**
     * @param Magento_AuthorizationInterface $authorization
     * @param array $args
     */
    public function __construct(Magento_AuthorizationInterface $authorization, array $args = array())
    {
        $this->_authorizationModel = $authorization;
        parent::__construct($args);
    }

    /**
     * Generate row url
     * @param Magento_Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if ($this->_authorizationModel->isAllowed('Magento_SalesArchive::orders')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
