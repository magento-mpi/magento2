<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration comment model interface
 */
namespace Magento\Backend\Model\Config;

interface CommentInterface
{
    /**
     * Retrieve element comment by element value
     * @param mixed $elementValue
     * @return string
     */
    public function getCommentText($elementValue);
}
