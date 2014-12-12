<?php
/**
 * Application config file resolver
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\Document\Outline;

use Magento\Framework\Config\ValidationStateInterface;

class ValidationState implements ValidationStateInterface
{
    /**
     * @var string
     */
    protected $appMode;

    /**
     * @param string $appMode
     */
    public function __construct($appMode)
    {
        $this->appMode = $appMode;
    }

    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidated()
    {
        return $this->appMode !== \Magento\Framework\App\State::MODE_PRODUCTION;
    }
}
