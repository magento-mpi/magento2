<?php
/**
 * Application response
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface ResponseInterface extends \Magento\HTTP\ResponseInterface
{
    /**
     * Set body content
     *
     * @param  string $content
     * @param  null|string $name
     * @return self
     */
    public function setBody($content, $name = null);

    /**
     * Append content to the body content
     *
     * @param  string $content
     * @param  null|string $name
     * @return self
     */
    public function appendBody($content, $name = null);

    /**
     * Clear body array
     *
     * @param  string $name Named segment to clear
     * @return boolean
     */
    public function clearBody($name = null);

    /**
     * Return the body content
     *
     * @param boolean $spec
     * @return string|array|null
     */
    public function getBody($spec = false);

    /**
     * Append a named body segment to the body content array
     *
     * @param  string $name
     * @param  string $content
     * @return self
     */
    public function append($name, $content);

    /**
     * Prepend a named body segment to the body content array
     *
     * @param  string $name
     * @param  string $content
     * @return void
     */
    public function prepend($name, $content);

    /**
     * Insert a named segment into the body content array
     *
     * @param  string $name
     * @param  string $content
     * @param  string $parent
     * @param  boolean $before Whether to insert the new segment before or
     * after the parent. Defaults to false (after)
     * @return self
     */
    public function insert($name, $content, $parent = null, $before = false);

    /**
     * Echo the body segments
     *
     * @return void
     */
    public function outputBody();

    /**
     * Register an exception with the response
     *
     * @param  \Exception $e
     * @return self
     */
    public function setException(\Exception $e);

    /**
     * Retrieve the exception stack
     *
     * @return array
     */
    public function getException();

    /**
     * Has an exception been registered with the response?
     *
     * @return boolean
     */
    public function isException();

    /**
     * Does the response object contain an exception of a given type?
     *
     * @param  string $type
     * @return boolean
     */
    public function hasExceptionOfType($type);

    /**
     * Does the response object contain an exception with a given message?
     *
     * @param  string $message
     * @return boolean
     */
    public function hasExceptionOfMessage($message);

    /**
     * Does the response object contain an exception with a given code?
     *
     * @param  int $code
     * @return boolean
     */
    public function hasExceptionOfCode($code);

    /**
     * Retrieve all exceptions of a given type
     *
     * @param  string $type
     * @return false|array
     */
    public function getExceptionByType($type);

    /**
     * Retrieve all exceptions of a given message
     *
     * @param  string $message
     * @return false|array
     */
    public function getExceptionByMessage($message);

    /**
     * Retrieve all exceptions of a given code
     *
     * @param  mixed $code
     * @return void
     */
    public function getExceptionByCode($code);

    /**
     * Whether or not to render exceptions (off by default)
     *
     * If called with no arguments or a null argument, returns the value of the
     * flag; otherwise, sets it and returns the current value.
     *
     * @param  boolean $flag Optional
     * @return boolean
     */
    public function renderExceptions($flag = null);

    /**
     * Send response to client
     */
    public function sendResponse();
}
