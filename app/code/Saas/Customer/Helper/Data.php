<?php
/**
 * Functionality limitation checker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Customer_Helper_Data extends Enterprise_Customer_Helper_Data
{

    /**
     * Returns everything from parent's method except image and file.
     * @param null $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $types = parent::getAttributeInputTypes($inputType);
        if (null === $inputType) {
            unset($types['file']);
            unset($types['image']);
        } else {
            if ($inputType == 'file' || $inputType == 'image') {
                return array();
            }
        }
        return $types;
    }

}
