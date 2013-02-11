<?php
/**
 * FPC processor interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Enterprise_PageCache_Model_Cache_ProcessorInterface
    extends Enterprise_PageCache_Model_RequestProcessorInterface
{
    /**
     * Get HTTP request identifier
     *
     * @return string
     */
    public function getRequestId();


    /**
     * Get page identifier for loading page from cache
     * @return string
     */
    public function getRequestCacheId();


    /**
     * Check if processor is allowed for current HTTP request.
     * Disable processing HTTPS requests and requests with "NO_CACHE" cookie
     *
     * @return bool
     */
    public function isAllowed();

    /**
     * Retrieve recently viewed count cache identifier
     *
     * @return string
     */
    public function getRecentlyViewedCountCacheId();


    /**
     * Retrieve session info cache identifier
     *
     * @return string
     */
    public function getSessionInfoCacheId();

    /**
     * Associate tag with page cache request identifier
     *
     * @param array|string $tag
     * @return Enterprise_PageCache_Model_Processor
     */
    public function addRequestTag($tag);

    /**
     * Get cache request associated tags
     * @return array
     */
    public function getRequestTags();

    /**
     * Process response body by specific request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @return Enterprise_PageCache_Model_Processor
     */
    public function processRequestResponse(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response
    );

    /**
     * Do basic validation for request to be cached
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function canProcessRequest(Zend_Controller_Request_Http $request);

    /**
     * Get specific request processor based on request parameters.
     *
     * @param Zend_Controller_Request_Http $request
     * @return Enterprise_PageCache_Model_Processor_Default
     */
    public function getRequestProcessor(Zend_Controller_Request_Http $request);

    /**
     * Set metadata value for specified key
     *
     * @param string $key
     * @param string $value
     *
     * @return Enterprise_PageCache_Model_Processor
     */
    public function setMetadata($key, $value);

    /**
     * Get metadata value for specified key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getMetadata($key);

    /**
     * Set subprocessor
     *
     * @param Enterprise_PageCache_Model_Cache_SubProcessorInterface $subProcessor
     */
    public function setSubprocessor(Enterprise_PageCache_Model_Cache_SubProcessorInterface $subProcessor);

    /**
     * Get subprocessor
     *
     * @return Enterprise_PageCache_Model_Cache_SubProcessorInterface
     */
    public function getSubprocessor();
}
