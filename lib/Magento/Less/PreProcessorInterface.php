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
     * @param string $lessContent
     * @param array $viewParams
     * @param array $params
     * @return string of processed content
     */
    public function process($lessContent, array $viewParams, array $params = []);
}
