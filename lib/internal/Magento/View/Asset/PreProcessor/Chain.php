<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

/**
 * An object that's passed to preprocessors to carry current and original information for processing
 * Encapsulates complexity of all necessary context and parameters
 */
class Chain
{
    /**
     * @var \Magento\View\Asset\LocalInterface
     */
    private $asset;

    /**
     * @var string
     */
    private $origContent;

    /**
     * @var string
     */
    private $origContentType;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @param \Magento\View\Asset\LocalInterface $asset
     * @param string $origContent
     * @param string $origContentType
     */
    public function __construct(\Magento\View\Asset\LocalInterface $asset, $origContent, $origContentType)
    {
        $this->asset = $asset;
        $this->origContent = $origContent;
        $this->origContentType = $origContentType;
        $this->content = $origContent;
        $this->contentType = $asset->getContentType();
    }

    /**
     * Get asset object
     *
     * @return \Magento\View\Asset\LocalInterface
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * Get original content
     *
     * @return string
     */
    public function getOrigContent()
    {
        return $this->origContent;
    }

    /**
     * Get current content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set current content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get original content type
     *
     * @return string
     */
    public function getOrigContentType()
    {
        return $this->origContentType;
    }

    /**
     * Get current content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set current content type
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Get the intended content type
     *
     * @return string
     */
    public function getTargetContentType()
    {
        return $this->asset->getContentType();
    }

    /**
     * Assert invariants
     *
     * Impose an integrity check to avoid generating mismatching content type
     *
     * @throws \LogicException
     */
    public function assertValid()
    {
        $targetType = $this->getTargetContentType();
        if ($this->contentType !== $targetType) {
            throw new \LogicException(
                "The requested asset type was '{$targetType}', but ended up with '{$this->contentType}'"
            );
        }
    }

    /**
     * Whether materialization is necessary for the result of changes
     *
     * @return bool
     */
    public function isMaterializationRequired()
    {
        return $this->origContentType != $this->contentType || $this->origContent != $this->content;
    }
} 
