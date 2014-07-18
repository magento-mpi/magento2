<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Controller\Adminhtml;

use Magento\Framework\Exception\InputException;

/**
 * Adminhtml common tax class controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Tax\Service\V1\TaxClassServiceInterface
     */
    protected $taxClassService;

    /**
     * @var \Magento\Tax\Service\V1\Data\TaxClassBuilder
     */
    protected $taxClassBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService
     * @param \Magento\Tax\Service\V1\Data\TaxClassBuilder $taxClassBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService,
        \Magento\Tax\Service\V1\Data\TaxClassBuilder $taxClassBuilder
    ) {
        $this->taxClassService = $taxClassService;
        $this->taxClassBuilder = $taxClassBuilder;
        parent::__construct($context);
    }

    /**
     * Validate/Filter Tax Class Name
     *
     * @param string $className
     * @return string processed class name
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function _processClassName($className)
    {
        $className = trim($this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($className));
        if ($className == '') {
            throw new InputException('Invalid name of tax class specified.');
        }
        return $className;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Tax::manage_tax');
    }
}
