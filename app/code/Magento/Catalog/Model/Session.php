<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog session model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Session\Config\ConfigInterface $sessionConfig
     * @param \Zend_Session_SaveHandler_Interface $saveHandler
     * @param \Magento\Session\ValidatorInterface $validator
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Session\Config\ConfigInterface $sessionConfig,
        \Zend_Session_SaveHandler_Interface $saveHandler,
        \Magento\Session\ValidatorInterface $validator,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($context, $sidResolver, $sessionConfig, $saveHandler, $validator, $data);
        $this->start('catalog', $sessionName);
    }

    public function getDisplayMode()
    {
        return $this->_getData('display_mode');
    }
}
