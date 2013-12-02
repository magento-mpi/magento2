<?php
/**
 * Backend user session
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Core\Model\Session\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->init('adminhtml');
    }

    /**
     * Skip path validation in backend area
     *
     * @param string $path
     * @return bool
     */
    public function isValidForPath($path)
    {
        return true;
    }

    /**
     * Always try to get session id from query in backend area
     *
     * @return bool
     */
    protected function _isSidUsedFromQueryParam()
    {
        return true;
    }
}
