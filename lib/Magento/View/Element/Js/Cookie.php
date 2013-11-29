<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Js;

use Magento\Cookie\ConfigInterface;
use Magento\Core\Helper\Data;
use Magento\View\Element\Template;
use Magento\View\Element\Template\Context;

class Cookie extends Template
{
    /**
     * @var ConfigInterface
     */
    protected $_cookieConfig;

    /**
     * @param Context $context
     * @param Data $coreData
     * @param ConfigInterface $cookieConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $coreData,
        ConfigInterface $cookieConfig,
        array $data = array()
    ) {
        $this->_cookieConfig = $cookieConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->_cookieConfig->getDomain();
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
        return $this->_cookieConfig->getPath();
    }
}
