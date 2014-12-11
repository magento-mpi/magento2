<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\Ui\Widget\Navigation;

use Magento\Framework\Module\ModuleList;
use Magento\Framework\View\Element\Template;

class Module extends Template
{
    /**
     * @var ModuleList
     */
    protected $moduleList;

    /**
     * @param Template\Context $context
     * @param ModuleList $moduleList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModuleList $moduleList,
        array $data = [])
    {
        $this->moduleList = $moduleList;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve active modules array
     *
     * @return array
     */
    public function getModules()
    {
        return $this->moduleList->getAll();
    }
}
