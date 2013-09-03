<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend abstract block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_Abstract extends Magento_Core_Block_Abstract
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(Magento_Backend_Block_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
}
