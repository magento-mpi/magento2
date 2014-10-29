<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\Util\Generate;

use Magento\Framework\App;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Framework\ObjectManager;

/**
 * Class TestCaseClass
 * TestCase Classes generator based on Jira ticket
 *
 * @api
 */
class TestCase extends AbstractGenerate
{
    /**
     * Jira configuration
     *
     * @var array
     */
    protected $config = [];

    /**
     * Default path to Jira configuration file
     *
     * @var string
     */
    protected $jiraConfigFile = 'config/jira.yml';

    /**
     * @constructor
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct($objectManager);

        $config = $objectManager->create('Mtf\System\Config', ['filePath' => $this->getJiraConfigPath()]);
        $this->config = $config->getParamByPath('jira');
    }

    /**
     * Get jira config file path
     *
     * @return string
     */
    protected function getJiraConfigPath()
    {
        return file_exists(MTF_BP . '/' . $this->jiraConfigFile)
            ? $this->jiraConfigFile
            : $this->jiraConfigFile . '.dist';
    }

    /**
     * Generate test cases
     *
     * @retun void
     */
    public function launch()
    {
        $this->generateXml();
        $this->generateClasses();
    }

    /**
     * Generate TestCase XML
     *
     * @retun void
     */
    public function generateXml()
    {
        $xml = simplexml_load_file(__DIR__ . '/testcase.xml');
        $xmlObject = new \SimpleXMLElement($xml->asXML());

        foreach ($xmlObject as $item) {
            $data = $this->getTicketData((array)$item);

            foreach ($data as $key => $field) {
                if ($item->xpath($key)) {
                    continue;
                }
                if (!is_array($field)) {
                    $item->addChild($key, $field);
                } else {
                    $node = $item->addChild($key);
                    foreach ($field as $value) {
                        $node->addChild(substr($key, 0, -1), $value);
                    }
                }
            }
            $this->cnt++;
        }

        // Format XML file
        $dom = new \DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlObject->asXML());
        $dom->save(__DIR__ . '/testcase.xml');

        \Mtf\Util\Generate\GenerateResult::addResult('Test Case Tickets', $this->cnt);
    }

    /**
     * Connect to Jira for getting information about ticket
     *
     * @param string $jiraTicket
     *
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getTicketData($jiraTicket)
    {
        if (!isset($jiraTicket['id'])) {
            throw new \Exception('Test case item #' . $this->cnt . ' does not have ticket id.');
        }
        $ticketId = $jiraTicket['id'];
        $credentials = $this->config['username'] . ':'
            . $this->config['password'];
        $url = $this->config['url'] . 'issue/' . $ticketId;

        $curl = new CurlTransport();
        $curl->setOptions([CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERPWD => $credentials]);
        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        $issue = json_decode($response, true);
        if ($issue === null) {
            throw new \Exception('Connection to Jira has been failed, verify jira config file settings.');
        }

        $ticketData = [];
        $ticketData['ticketId'] = $jiraTicket['id'];
        // Get ticket name
        $stringRemove = [
            'Test Creation for ',
        ];

        $ticketData['name'] = str_replace(' ', '', ucwords(str_replace($stringRemove, '', $issue['fields']['summary'])))
            . 'Test';
        $ticketData['description'] = $issue['fields']['summary'];

        // Get 'Magento Module' value
        $ticketData['module'] = isset($issue['fields']['customfield_13222']['value'])
            ? $issue['fields']['customfield_13222']['value']
            : null;

        // Get ticket components
        if (isset($issue['fields']['components'])) {
            foreach ($issue['fields']['components'] as $component) {
                $ticketData['components'][] = $component['name'];
            }
        }

        // Get test case steps form ticket description
        $stepsDelimiter = "*Test Flow:*\r\n";
        preg_match('/' . preg_quote($stepsDelimiter) . '((\s)*\S)*/', $issue['fields']['description'], $matches);

        $ticketData['steps'] = empty($matches)
            ? []
            : explode("\r\n", str_replace([$stepsDelimiter, '#'], '', $matches[0]));

        return $ticketData;
    }

    /**
     * Generate Test Cases Classes
     *
     * @return void
     */
    public function generateClasses()
    {
        $this->cnt = 0;

        $xmlObject = simplexml_load_file(__DIR__ . '/testcase.xml');
        foreach ($xmlObject as $item) {
            /** @var $item \SimpleXMLElement */
            $this->generateTestCaseClass($item);
        }

        \Mtf\Util\Generate\GenerateResult::addResult('Test Case Classes', $this->cnt);
    }

    /**
     * Generate test case class from XML source
     *
     * @param \SimpleXMLElement $item
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function generateTestCaseClass(\SimpleXMLElement $item)
    {
        $className = (string)$item->name;
        $ticketId = (string)$item->ticketId;
        $description = (string)$item->description;
        $module = property_exists($item->attributes(), 'module')
            ? (string)$item->attributes()->module
            : 'Magento\\' . (string)$item->module;
        $namespace = property_exists($item, 'namespace')
            ? (string)$item->namespace
            : $module . '\\Test\\TestCase';
        $steps = (array)$item->steps->step;
        $groups = (array)$item->components->component;

        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * {license_notice}\n";
        $content .= " *\n";
        $content .= " * @copyright   {copyright}\n";
        $content .= " * @license     {license_link}\n";
        $content .= " */\n\n";
        $content .= "namespace {$namespace};\n\n";
        $content .= "use Mtf\\TestCase\\Injectable;\n\n";

        $content .= "/**\n";
        $content .= " * {$description}\n";
        $content .= " *\n";
        $content .= " * Test Flow:";
        $stepNum = 1;
        foreach ($steps as $step) {
            $content .= "\n * {$stepNum}." . htmlspecialchars_decode($step);
            ++$stepNum;
        }
        if ($groups) {
            $content .= "\n *";
            $content .= "\n * @group ";
            $content .= implode(', ', str_replace(' ', '_', $groups));
        }
        $content .= "\n * @ZephyrId {$ticketId}\n";
        $content .= " */\n";

        $content .= "class {$className} extends Injectable\n";
        $content .= "{\n";

        $injectArgumentsArray = [];
        $injectArgumentsXml = $item->xpath('inject');
        if ($injectArgumentsXml) {
            foreach ($injectArgumentsXml[0] as $injectArgument) {
                $injectArgumentsArray[] = $injectArgument->class . ' $'
                    . lcfirst($this->toCamelCase($injectArgument->getName()));
            }
        }
        $injectArguments = implode(', ', $injectArgumentsArray);

        $content .= '    public function __inject(' . $injectArguments . ')' . "\n";
        $content .= "    {\n";
        $content .= "        // \n";
        $content .= "    }\n\n";

        $invokeArgumentsArray = [];
        $invokeArgumentsXml = $item->xpath('invoke');
        if ($invokeArgumentsXml) {
            foreach ($invokeArgumentsXml[0] as $invokeArgument) {
                $invokeArgumentsArray[] = $invokeArgument->class . ' $'
                    . lcfirst($this->toCamelCase($invokeArgument->getName()));
            }
        }
        $invokeArguments = implode(', ', $invokeArgumentsArray);

        $content .= '    public function test(' . $invokeArguments . ')' . "\n";
        $content .= "    {\n";
        $content .= "        // \n";
        $content .= "    }\n";
        $content .= "}\n";

        $generatedFolderPath =  MTF_TESTS_PATH . str_replace('\\', '/', $namespace);
        $newFileName = $className . '.php';
        $newFilePath = $generatedFolderPath . '/' . $newFileName;

        if (file_exists($newFilePath)) {
            return;
        }

        if (!is_dir($generatedFolderPath)) {
            mkdir($generatedFolderPath, 0777, true);
        }

        file_put_contents($newFilePath, $content);
        touch($newFilePath);

        $this->cnt++;
    }
}
