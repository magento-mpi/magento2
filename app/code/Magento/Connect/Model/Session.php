<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Model;

/**
 * Auth session model
 */
class Session extends \Magento\Session\SessionManager
{
    /**
     * Connect data
     *
     * @var \Magento\Connect\Helper\Data
     */
    protected $_connectData;

    /**
     * @param \Magento\App\Request\Http $request
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Session\ValidatorInterface $validator
     * @param \Magento\Session\StorageInterface $storage
     * @param \Magento\Connect\Helper\Data $connectData
     */
    public function __construct(
        \Magento\App\Request\Http $request,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Session\SaveHandlerInterface $saveHandler,
        \Magento\Session\ValidatorInterface $validator,
        \Magento\Session\StorageInterface $storage,
        \Magento\Connect\Helper\Data $connectData
    ) {
        $this->_connectData = $connectData;
        parent::__construct($request, $sidResolver, $sessionConfig, $saveHandler, $validator, $storage);
        $this->start();
    }

    /**
     * Retrieve parameters of extension from session.
     * Compatible with old version extension info file.
     *
     * @return array
     */
    public function getCustomExtensionPackageFormData()
    {
        $data = $this->getData('custom_extension_package_form_data');
        /* convert Maintainers to Authors */
        if (!isset($data['authors']) || count($data['authors']) == 0) {
            if (isset($data['maintainers'])) {
                $data['authors']['name'] = array();
                $data['authors']['user'] = array();
                $data['authors']['email'] = array();
                foreach ($data['maintainers']['name'] as $i => $name) {
                    if (!$data['maintainers']['name'][$i] &&
                        !$data['maintainers']['handle'][$i] &&
                        !$data['maintainers']['email'][$i]
                    ) {
                        continue;
                    }
                    $data['authors']['name'][] = $data['maintainers']['name'][$i];
                    $data['authors']['user'][] = $data['maintainers']['handle'][$i];
                    $data['authors']['email'][] = $data['maintainers']['email'][$i];
                }
                // Convert channel from previous version for entire package
                $helper = $this->_connectData;
                if (isset($data['channel'])) {
                    $data['channel'] = $helper->convertChannelFromV1x($data['channel']);
                }
                // Convert channel from previous version for each required package
                $nRequiredPackages = count($data['depends']['package']['channel']);
                for ($i = 0; $i < $nRequiredPackages; $i++) {
                    $channel = $data['depends']['package']['channel'][$i];
                    if ($channel) {
                        $data['depends']['package']['channel'][$i] = $helper->convertChannelFromV1x($channel);
                    }
                }
            }
        }

        /* convert Release version to Version */
        if (!isset($data['version'])) {
            if (isset($data['release_version'])) {
                $data['version'] = $data['release_version'];
            }
        }
        /* convert Release stability to Stability */
        if (!isset($data['stability'])) {
            if (isset($data['release_stability'])) {
                $data['stability'] = $data['release_stability'];
            }
        }
        /* convert contents */
        if (!isset($data['contents']['target'])) {
            $data['contents']['target'] = $data['contents']['role'];
        }
        return $data;
    }
}
