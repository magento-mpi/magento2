<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid row url generator
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Grid\Row;

class UrlGenerator extends \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     * @param array $args
     */
    public function __construct(\Magento\AuthorizationInterface $authorization, array $args = array())
    {
        $this->_authorization = $authorization;
        parent::__construct($args);

    }

    /**
     * Generate row url
     * @param \Magento\Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if ($this->_authorization->isAllowed('Magento_Sales::actions_view')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
