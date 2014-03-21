<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Translate\Inline;

/**
 * Proxy class for \Magento\Translate\Inline
 */
class Proxy extends \Magento\Translate\Inline
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento\DesignEditor\Model\Translate\Inline
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Translate\Inline',
        $shared = true
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_subject', '_isShared');
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\DesignEditor\Model\Translate\Inline
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return $this->_getSubject()->isAllowed();
    }

    /**
     * {@inheritdoc}
     */
    public function getParser()
    {
        return $this->_getSubject()->getParser();
    }

    /**
     * {@inheritdoc}
     */
    public function processResponseBody(&$body, $isJson = false)
    {
        return $this->_getSubject()->processResponseBody($body, $isJson);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return $this->_getSubject()->getAdditionalHtmlAttribute($tagName);
    }
}
