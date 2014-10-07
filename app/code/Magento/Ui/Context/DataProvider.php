<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Context;

use Magento\Ui\Component\AbstractView;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractView
{
    /**
     * @return string
     */
    public function getAsJson()
    {
        return $this->renderContext->getConfigBuilder()->toJson($this->renderContext->getStorage());
    }
}
