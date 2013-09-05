<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract block context object. Will be used as rule condition constructor modification point after release.
 * Important: Should not be modified by extension developers.
 */
class Magento_Rule_Model_Condition_Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @param Magento_Core_Model_View_Url $viewUrl
     */
    public function __construct(Magento_Core_Model_View_Url $viewUrl)
    {
        $this->_viewUrl = $viewUrl;
    }

    /**
     * @return Magento_Core_Model_View_Url
     */
    public function getViewUrl()
    {
        return $this->_viewUrl;
    }
}
