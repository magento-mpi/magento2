<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less;

/**
 * Interface for pre-processing less instruction
 */
interface PreProcessorInterface
{
    /**
     * Process less content to adapt magento view system
     *
     * @param PreProcessor\File\Less $lessFile
     * @param string $lessContent
     * @return string of processed content
     */
    public function process(PreProcessor\File\Less $lessFile, $lessContent);
}
