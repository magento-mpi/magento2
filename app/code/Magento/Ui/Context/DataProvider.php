<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Context;

use Magento\Ui\AbstractView;

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
        return $this->renderContext->getConfigurationBuilder()->toJson($this->renderContext->getStorage());
    }
}
