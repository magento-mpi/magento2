<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sales\Model\Email;

class Template extends \Magento\Core\Model\Email\Template
{
    /**
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function getInclude($template, array $variables)
    {
        $filename = $this->_viewFileSystem->getFilename($template);
        if (!$filename) {
            return '';
        }
        extract($variables);
        ob_start();
        include $filename;
        return ob_get_clean();
    }
}
