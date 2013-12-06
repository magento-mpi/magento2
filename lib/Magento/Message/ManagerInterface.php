<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Message manager interface
 */
interface ManagerInterface
{
    /**
     * Default message group
     */
    const DEFAULT_GROUP = 'default';

    /**
     * Retrieve messages
     *
     * @param string $group
     * @param bool $clear
     * @return Collection
     */
    public function getMessages($group = self::DEFAULT_GROUP, $clear = false);

    /**
     * Adding new message to message collection
     *
     * @param MessageInterface $message
     * @param string $group
     * @return ManagerInterface
     */
    public function addMessage(MessageInterface $message, $group = self::DEFAULT_GROUP);

    /**
     * Adding messages array to message collection
     *
     * @param array $messages
     * @param string $group
     * @return ManagerInterface
     */
    public function addMessages(array $messages, $group = self::DEFAULT_GROUP);

    /**
     * Adding new error message
     *
     * @param string $message
     * @param string $group
     * @return ManagerInterface
     */
    public function addError($message, $group = self::DEFAULT_GROUP);

    /**
     * Adding new warning message
     *
     * @param string $message
     * @param string $group
     * @return ManagerInterface
     */
    public function addWarning($message, $group = self::DEFAULT_GROUP);

    /**
     * Adding new notice message
     *
     * @param string $message
     * @param string $group
     * @return ManagerInterface
     */
    public function addNotice($message, $group = self::DEFAULT_GROUP);

    /**
     * Adding new success message
     *
     * @param string $message
     * @param string $group
     * @return ManagerInterface
     */
    public function addSuccess($message, $group = self::DEFAULT_GROUP);

    /**
     * Adds messages array to message collection, but doesn't add duplicates to it
     *
     * @param array|MessageInterface $messages
     * @param string $group
     * @return ManagerInterface
     */
    public function addUniqueMessages($messages, $group = self::DEFAULT_GROUP);

    /**
     * Not Magento exception handling
     *
     * @param \Exception $exception
     * @param string $alternativeText
     * @param string $group
     * @return ManagerInterface
     */
    public function addException(\Exception $exception, $alternativeText, $group = self::DEFAULT_GROUP);
}
