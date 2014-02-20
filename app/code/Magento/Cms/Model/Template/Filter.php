<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Model\Template;

/**
 * Cms Template Filter Model
 */
class Filter extends \Magento\Email\Model\Template\Filter
{
    /**
     * Whether to allow SID in store directive: AUTO
     *
     * @var bool
     */
    protected $_useSessionInUrl;

    /**
     * Setter whether SID is allowed in store directive
     *
     * @param bool $flag
     * @return $this
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }
}
