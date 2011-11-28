<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    publisher
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__FILE__) . '/ScriptAbstract.php';

/**
 * Merge changes from one repository to another without commit
 *
 */
class Merger extends ScriptAbstract {

    /**
     * Repository merging from
     *
     * @var string
     */
    private $_resourceRepositoryPath;

    /**
     * Repository merging to
     *
     * @var string
     */
    private $_goalRepositoryPath;

    /**
     * Set resource repository path
     *
     * @param $path
     * @return Merger
     */
    public function setResourceRepositoryPath($path)
    {
        $this->_resourceRepositoryPath = $path;
        return $this;
    }

    /**
     * Set goal repository path
     *
     * @param $path
     * @return Merger
     */
    public function setGoalRepositoryPath($path)
    {
        $this->_goalRepositoryPath = $path;
        return $this;
    }

    /**
     * Set goal repository clone path
     *
     * @param $path
     * @return Merger
     */
    public function setGoalRepositoryClonePath($path)
    {
        $this->_repositoryPath = $path;
        return $this;
    }

    /**
     * Merge changes from one repository to another without commit
     *
     * @return void
     */
    public function run()
    {
        // Create a clone of the goal repository
        if (!is_dir($this->_repositoryPath)) {
            $exitCode = $this->_execCmd("git clone {$this->_goalRepositoryPath} {$this->_repositoryPath}");
            if ($exitCode !== 0) {
                $this->_throwException("Can not be created like a copy of {$this->_goalRepositoryPath}.");
            }
        } else {
            if (!$this->_isRemoteExist('origin')) {
               $this->_throwException("Required remote 'origin' is not exist.");
            }
            $remoteInfo = $this->_getRemoteInfo('origin');
            if ($remoteInfo['push_url'] != $this->_goalRepositoryPath) {
               $this->_throwException("Is not a clone of repository {$this->_goalRepositoryPath}.");
            }
        }

        // The clone of the goal repository has to contain commits
        $output = $this->_callGitCommand('log -1');
        if (!$output || !isset($output[0]) || strpos($output[0], 'commit ') !== 0) {
            $this->_callGitCommand(
                'commit --allow-empty --message="Initialising"',
                'Can not do commit.'
            );
        }

        // The clone of the goal repository has to contain the resource repository as a remote
        if (!$this->_isRemoteExist('resource')) {
            $this->_callGitCommand(
                "remote add resource {$this->_resourceRepositoryPath}",
                "Can not add repository {$this->_resourceRepositoryPath} as a remote 'resource'."
            );
        } else {
            $remoteInfo = $this->_getRemoteInfo('resource');
            if ($remoteInfo['fetch_url'] != $this->_resourceRepositoryPath) {
                $this->_throwException("Remote 'resource' is not the resource repository {$this->_resourceRepositoryPath}.");
            }
        }

        // Merge and reset the index
        $this->_callGitCommand(
            'fetch resource',
            'Can not fetch remote \'resource\'.'
        );
        $this->_callGitCommand(
            'merge --no-commit --squash remotes/resource/master',
            'Can not merge remote \'resource\'.'
        );
        $this->_callGitCommand(
            'reset',
            'Can not reset.'
        );
    }

    /**
     * Return true if the specified remote exist
     *
     * @param string $remoteName
     * @return bool
     */
    protected function _isRemoteExist($remoteName)
    {
        $output = $this->_callGitCommand(
            'remote',
            'Can not get information about remotes.'
        );
        return in_array($remoteName, $output);
    }

    /**
     * Return information of the specified remote
     *
     * @param string $remoteName
     * @return array
     */
    protected function _getRemoteInfo($remoteName)
    {
        $output = $this->_callGitCommand("remote show {$remoteName}");
        if ($output && isset($output[1]) && isset($output[2])) {
            return array(
                'fetch_url' => substr($output[1], 13),
                'push_url' => substr($output[2], 13),
            );
        }
        $this->_throwException("Can not get information about remote '{$remoteName}'.");
    }
}
