<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model;

/**
 * Session model
 */
class Session extends \Magento\Session\SessionManager
{
    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Session\ValidatorInterface $validator
     * @param \Magento\Session\StorageInterface $storage
     * @param null $sessionName
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Session\SaveHandlerInterface $saveHandler,
        \Magento\Session\ValidatorInterface $validator,
        \Magento\Session\StorageInterface $storage,
        $sessionName = null
    ) {
        parent::__construct($request, $sidResolver, $sessionConfig, $saveHandler, $validator, $storage);
        $this->start($sessionName);
    }
}
