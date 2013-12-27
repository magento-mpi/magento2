<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Processor;

class Fixture extends \Magento\FullPageCache\Model\Processor
{
    /**
     * Expose the parent's protected process content method to unit tests
     *
     * @param string $content
     * @param \Zend_Controller_Request_Http $request
     * @return string|false
     */
    public function processContent($content, \Zend_Controller_Request_Http $request)
    {
        return parent::_processContent($content, $request);
    }

}