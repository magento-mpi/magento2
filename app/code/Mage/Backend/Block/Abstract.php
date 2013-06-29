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
 * Backend abstract block
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Block_Abstract extends Mage_Core_Block_Abstract
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Mage_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(Mage_Backend_Block_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
}
