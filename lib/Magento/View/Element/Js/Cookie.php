<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Js;

use Magento\Session\Config\ConfigInterface;
use Magento\View\Element\Template;
use Magento\View\Element\Template\Context;

class Cookie extends Template
{
    /**
     * Session config
     *
     * @var ConfigInterface
     */
    protected $sessionConfig;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ConfigInterface $cookieConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $cookieConfig,
        array $data = array()
    ) {
        $this->sessionConfig = $cookieConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->sessionConfig->getCookieDomain();
        if (!empty($domain[0]) && ($domain[0] !== '.')) {
            $domain = '.'.$domain;
        }
        return $domain;
    }

    /**
     * Get configured cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->sessionConfig->getCookiePath();
    }
}
