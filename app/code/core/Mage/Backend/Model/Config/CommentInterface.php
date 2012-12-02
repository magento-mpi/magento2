<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration comment model interface
 */
interface Mage_Backend_Model_Config_CommentInterface
{
    /**
     * Retrieve element comment by element value
     * @param mixed $elementValue
     * @return string
     */
    public function getCommentText($elementValue);
}
