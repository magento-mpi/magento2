<?php
/**
 * Captcha interface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Captcha\Model;

interface ModelInterface
{
    /**
     * Generates captcha
     *
     * @abstract
     * @return void
     */
    public function generate();

    /**
     * Checks whether word entered by user corresponds to the one generated by generate()
     *
     * @param string $word
     * @return bool
     * @abstract
     */
    public function isCorrect($word);

    /**
     * Get Block Name
     * @return string
     */
    public function getBlockName();
}
