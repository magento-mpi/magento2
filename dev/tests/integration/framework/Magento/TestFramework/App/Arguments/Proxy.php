<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\App\Arguments;

/**
 * Proxy class for \Magento\Framework\App\Arguments
 */
class Proxy extends \Magento\Framework\App\Arguments
{
    /**
     * Proxied instance
     *
     * @var \Magento\Framework\App\Arguments
     */
    protected $subject;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\App\Arguments $subject
     */
    public function __construct(\Magento\Framework\App\Arguments $subject)
    {
        $this->setSubject($subject);
    }

    /**
     * Set new subject to be proxied
     *
     * @param \Magento\Framework\App\Arguments $subject
     */
    public function setSubject(\Magento\Framework\App\Arguments $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection($connectionName)
    {
        return $this->subject->getConnection($connectionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getConnections()
    {
        return $this->subject->getConnections();
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->subject->getResources();
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheFrontendSettings()
    {
        return $this->subject->getCacheFrontendSettings();
    }

    /**
     * Retrieve identifier of a cache frontend, configured to be used for a cache type
     *
     * @param string $cacheType Cache type identifier
     * @return string|null
     */
    public function getCacheTypeFrontendId($cacheType)
    {
        return $this->subject->getCacheTypeFrontendId($cacheType);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key = null, $defaultValue = null)
    {
        return $this->subject->get($key, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public function reload()
    {
        return $this->subject->reload();
    }
}
