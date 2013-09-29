<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Processor;

class DefaultProcessor implements \Magento\FullPageCache\Model\Cache\SubProcessorInterface
{
    /**
     * @var \Magento\FullPageCache\Model\Container\Placeholder
     */
    private $_placeholder;

    /**
     * Disable cache for url with next GET params
     *
     * @var array
     */
    protected $_noCacheGetParams = array('___store', '___from_store');

    /**
     * Check if request can be cached
     *
     * @param \Zend_Controller_Request_Http $request
     * @return bool
     */
    public function allowCache(\Zend_Controller_Request_Http $request)
    {
        foreach ($this->_noCacheGetParams as $param) {
            if (!is_null($request->getParam($param, null))) {
                return false;
            }
        }
        if (\Mage::getSingleton('Magento\Core\Model\Session')->getNoCacheFlag()) {
            return false;
        }
        return true;
    }


    /**
     * Replace block content to placeholder replacer
     *
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    public function replaceContentToPlaceholderReplacer($content)
    {
        $placeholders = array();
        preg_match_all(
            \Magento\FullPageCache\Model\Container\Placeholder::HTML_NAME_PATTERN,
            $content,
            $placeholders,
            PREG_PATTERN_ORDER
        );
        $placeholders = array_unique($placeholders[1]);
        try {
            foreach ($placeholders as $definition) {
                $this->_placeholder = \Mage::getModel('Magento\FullPageCache\Model\Container\Placeholder',
                    array('definition' => $definition));
                $content = preg_replace_callback($this->_placeholder->getPattern(),
                    array($this, '_getPlaceholderReplacer'), $content);
            }
            $this->_placeholder = null;
        } catch (\Exception $e) {
            $this->_placeholder = null;
            throw $e;
        }
        return $content;
    }

    /**
     * Prepare response body before caching
     *
     * @param \Zend_Controller_Response_Http $response
     * @return string
     */
    public function prepareContent(\Zend_Controller_Response_Http $response)
    {
        return $this->replaceContentToPlaceholderReplacer($response->getBody());
    }

    /**
     * Retrieve placeholder replacer
     *
     * @param array $matches Matches by preg_replace_callback
     * @return string
     */
    protected function _getPlaceholderReplacer($matches)
    {
        $container = $this->_placeholder->getContainerClass();
        /**
         * In developer mode blocks will be rendered separately
         * This should simplify debugging _renderBlock()
         */
        if ($container && !\Mage::getIsDeveloperMode()) {
            $container = \Mage::getModel($container, array('placeholder' => $this->_placeholder));
            $container->setProcessor(\Mage::getSingleton('Magento\FullPageCache\Model\Processor'));
            $blockContent = $matches[1];
            $container->saveCache($blockContent);
        }
        return $this->_placeholder->getReplacer();
    }


    /**
     * Return cache page id with application. Depends on GET super global array.
     *
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return string
     */
    public function getPageIdInApp(\Magento\FullPageCache\Model\Processor $processor)
    {
        return $this->getPageIdWithoutApp($processor);
    }

    /**
     * Return cache page id without application. Depends on GET super global array.
     *
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return string
     */
    public function getPageIdWithoutApp(\Magento\FullPageCache\Model\Processor $processor)
    {
        $queryParams = $_GET;
        ksort($queryParams);
        $queryParamsHash = md5(serialize($queryParams));
        return $processor->getRequestId() . '_' . $queryParamsHash;
    }
}
