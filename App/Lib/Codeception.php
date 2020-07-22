<?php namespace App\Lib;

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Codeception
{
    /**
     * List of the Test sites
     *
     * @var array
     */
    private $sites;

    /**
     * Configuration for Codeception
     *
     * Merges the Codeception.yml and Webception Codeception.php
     *
     * @var boolean
     */
    public $config = FALSE;

    /**
     * Temporary copy of the Codeception.yml setup.
     *
     * If this is set, it means the configuration was loaded
     *
     * @var bool
     */
    private $yaml = FALSE;

    /**
     * Tally of all the tests that have been loaded
     *
     * @var integer
     */
    private $tally = 0;

    /**
     * List of all the tests
     *
     * @var array
     */
    private $tests = array();

    /**
     * Initialization of the Codeception class.
     *
     * @param array $config The codeception.php configuration file.
     */
    public function __construct($config = array(), $site = NULL)
    {
        // Set the basic config, just incase.
        $this->config = $config;

        // If the array wasn't loaded, we can't go any further.
        if (sizeof($config) == 0)
            return;

        // Setup the sites available to Webception
        $this->site = $site;

        // If the site class isn't ready, we can't load codeception.
        if (! $site->ready())
            return;

        // If the Configuration was loaded successfully, merge the configs!
        if ($this->yaml = $this->loadConfig($site->getConfigPath(), $site->getConfigFile())) {
            $this->config = array_merge($config, $this->yaml);
            $this->loadTests();
            $this->loadModules();
            $this->loadGroups();
        }
    }

    /**
     * Return if Codeception is ready.
     *
     * @return boolean
     */
    public function ready()
    {
        return $this->yaml !== FALSE;
    }

    /**
     * Load the Codeception YAML configuration.
     *
     * @param  string $path
     * @param  string $file
     * @return array  $config
     */
    public function loadConfig($path, $file)
    {
        $full_path = $path . $file;

        // If the Codeception YAML can't be found, the application can't go any further.
        if (! file_exists($full_path))
            return false;

        // Using Symfony's Yaml parser, the file gets turned into an array.
        $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($full_path));

        if (!isset($config['paths']) || !is_array($config['paths'])) {
            throw new \Exception("The config does not appear to contain any paths: ".$path.$file);
        }
        // Update the config to include the full path.
        foreach ($config['paths'] as $key => &$test_path) {
            $test_path = file_exists($path . $test_path) ?
                 realpath($path . $test_path) : $path . $test_path;
        }

        $config['env'] = array();

        if (isset($this->config['tests'])) {
            foreach ($this->config['tests'] as $type => $active) {

                if (! $active)
                    break;

                // eBRÁNA - support codeception configs with includes
                if (isset($config['include'])) {
                    $suite = [];
                    $config['paths']['tests'] = [];
                    foreach ($config['include'] as $includePath) {
                        $include = \Symfony\Component\Yaml\Yaml::parse($path.$includePath.DIRECTORY_SEPARATOR.'codeception.yml');
                        if (!is_array($include)) continue;

                        $config['paths']['tests'][] = $path.$includePath.DIRECTORY_SEPARATOR.$include['paths']['tests'];
                        $includeSuite = \Symfony\Component\Yaml\Yaml::parse($path.$includePath.DIRECTORY_SEPARATOR.$include['paths']['tests'].DIRECTORY_SEPARATOR."$type.suite.yml");
                        $suite = array_merge_recursive($suite, $includeSuite);
                    }
                } else {
                    $suite = \Symfony\Component\Yaml\Yaml::parse($config['paths']['tests'] . "/$type.suite.yml");
                }


                if ($suite) {
                    if (isset($suite['env'])) {
                        $config['env'][$type] = array_keys($suite['env']);
                    }
                }
            }
        }

        return $config;
    }


    /**
     * Load the Codeception tests from disk.
     */
    public function loadTests()
    {
        if (! isset($this->config['tests']))
            return;

        foreach ($this->config['tests'] as $type => $active) {
            // If the test type has been disabled in the Webception config,
            //      skip processing the directory read for those tests.
            if (! $active)
                break;

            $testPaths = is_array($this->config['paths']['tests']) ? $this->config['paths']['tests'] : [$this->config['paths']['tests']];
            foreach ($testPaths as $testPath) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator("{$testPath}" . $this->config['DS'] . "{$type}" . $this->config['DS'], \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                // Iterate through all the files, and filter out
                //      any files that are in the ignore list.
                foreach ($files as $file) {
                    if (!in_array($file->getFilename(), $this->config['ignore'])
                        && $file->isFile()) {
                        // Declare a new test and add it to the list.
                        $test = new Test();
                        $test->init($type, $file);
                        $this->addTest($test);
                        unset($test);
                    }

                }
            }
        }
    }

    /**
     * Load the Codeception tests from disk.
     */
    public function loadModules()
    {
        foreach ($this->config['modules'] as $name => $path) {
            $module = new Module();
            $module->init($name, $path);
            $this->addModule($module);
            unset($module);
        }
    }

    public function loadGroups()
    {
        foreach ($this->config['groups'] as $name) {
            $group = new Group();
            $group->init($name);
            $this->addGroup($group);
            unset($group);
        }
    }

    /**
     * Add a Test to the list.
     *
     * Push the tally count up as well.
     *
     * @param Test $test
     */
    public function addTest(Test $test)
    {
        $this->tally++;
        $this->tests[$test->getType()][$test->getHash()] = $test;
    }

    /**
     * Add a Module to the list.
     *
     * @param Test $test
     */
    public function addModule(Module $module)
    {
        $this->modules[$module->getType()][$module->getHash()] = $module;
    }

    /**
     * Add a Group to the list.
     *
     * @param Test $test
     */
    public function addGroup(Group $group)
    {
        $this->groups[$group->getName()] = $group;
    }

    /**
     * Get the complete test list.
     *
     * @param array $test List of loaded Tests.
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Get the complete modules list.
     *
     * @param array $module List of loaded Modules.
     */
    public function getModules()
    {
        return $this->modules;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Given a test type & hash, return a single Test.
     *
     * @param  string       $type Test type (Unit, Acceptance, Functional)
     * @param  string       $hash Hash of the test.
     * @return App\Lib\Test or FALSE.
     */
    public function getTest($type, $hash)
    {
        if (isset($this->tests[$type][$hash]))
            return $this->tests[$type][$hash];

        return FALSE;
    }

    public function getModule($type, $hash)
    {
        if (isset($this->modules[$type][$hash]))
            return $this->modules[$type][$hash];

        return FALSE;
    }

    public function getGroup($name)
    {
        if (isset($this->groups[$name]))
            return $this->groups[$name];

        return FALSE;
    }

    /**
     * Return the count of discovered tests
     *
     * @return integer $this->tally
     */
    public function getTestTally()
    {
        return $this->tally;
    }

    /**
     * Given a test, run the Codeception test.
     *
     * @param  Test $test Current test to Run.
     * @return Test $test Updated test with log and result.
     */
    public function run(Test $test)
    {
        $env = $this->getEnvironments($test->getType());

        // Get the full command path to run the test.
        $command = $this->getCommandPath($test->getType(), $test->getFilename(), $env);

        // Attempt to set the correct writes to Codeceptions Log path.
        @chmod($this->getLogPath(), 0777);

        // eBRÁNA - hack to set module-specific CWD
        $modulePath = preg_replace('/(.*application\/modules\/\w+)\/.*/', '$1', $test->getPathname());

        // Run the helper function (as it's not specific to Codeception)
        // which returns the result of running the terminal command into an array.
        $output  = array_merge([$command], run_terminal_command($command, $modulePath));

        // Add the log to the test which also checks to see if there was a pass/fail.
        $test->setLog($output);

        return $test;
    }

    /**
     * Given a module, run the Codeception module.
     *
     * @param  Module $module Current test to Run.
     * @return Module $module Updated test with log and result.
     */
    public function runModule(Module $module)
    {
        $env = $this->getEnvironments($module->getType());

        // Get the full command path to run the test.
        $command = $this->getCommandPath($module->getType(), '', $env);

        // Attempt to set the correct writes to Codeceptions Log path.
        @chmod($this->getLogPath(), 0777);

        // eBRANA hack to set module-specific CWD
        $modulePath = preg_replace('/(.*application\/modules\/\w+)\/.*/', '$1', $module->getPath());

        // Run the helper function (as it's not specific to Codeception)
        // which returns the result of running the terminal command into an array.
        $output  = array_merge([$modulePath, $command], run_terminal_command($command, $modulePath));

        // Add the log to the test which also checks to see if there was a pass/fail.
        $module->setLog($output);

        return $module;
    }

    /**
     * Given a module, run the Codeception module.
     *
     * @param  Group $module Current test to Run.
     * @return Group $module Updated test with log and result.
     */
    public function runGroup(Group $group)
    {
        $env = $this->getEnvironments($group->getType());

        // Get the full command path to run the test.
        $command = $this->getCommandPath('', '--group '.$group->getName(), $env);

        // Attempt to set the correct writes to Codeceptions Log path.
        @chmod($this->getLogPath(), 0777);

        // eBRÁNA - hack to set module-specific CWD
        $path = '/home/www/dvorak-platform.edevel.cz/application';

        // Run the helper function (as it's not specific to Codeception)
        // which returns the result of running the terminal command into an array.
        $output  = array_merge([$path, $command], run_terminal_command($command, $path));

        // Add the log to the test which also checks to see if there was a pass/fail.
        $group->setLog($output);

        return $group;
    }

    public function getEnvironments($type)
    {
        $env = array();
        if (isset($_GET['env'])) {

            foreach(explode(' ', $_GET['env']) as $value){
                if ($value) {

                    $value = str_replace($type . '_', '', $value);
                    if (isset($this->config['env'][$type]) && in_array($value, $this->config['env'][$type])) {
                        $env[] = '--env=' . $value;
                    }

                }
            }
        }

        return $env;
    }

    /**
     * Get the Codeception log path
     *
     * @return  string
     */
    public function getLogPath()
    {
        return $this->config['paths']['log'];
    }

    /**
     * Full command to run a Codeception test.
     *
     * @param  string $type     Test Type (Acceptance, Functional, Unit)
     * @param  string $filename Name of the Test
     * @param  string $env      Array like [ --env=envname ] if required
     * @return string Full command to execute Codeception with requred parameters.
     */
    public function getCommandPath($type, $filename, $env = [])
    {
        // Build all the different parameters as part of the console command
        $params = array_merge(
            array(
                $this->config['executable'],        // Codeception Executable
                "run",                              // Command to Codeception
                "--no-colors",                      // Forcing Codeception to not use colors, if enabled in codeception.yml
                //"--config=\"{$this->site->getConfig()}\"", // Full path & file of Codeception
            ),
            $env,
            array(
                $type,                              // Test Type (Acceptance, Unit, Functional)
                $filename,                          // Filename of the Codeception test
                "2>&1"                              // Added to force output of running executable to be streamed out
            )
        );

        //Run Codeception executable with a PHP command
        if(isset($this->config['run_php']) && $this->config['run_php']) {
            array_unshift($params, "php ");
        }
        //Add Debug command to command line if set in configuration
        if(isset($this->config['debug']) && $this->config['debug']) {
            $params[] = "--debug";
        }
        //Add Steps command to command line if set in configuration
        if(isset($this->config['steps']) && $this->config['steps']) {
            $params[] = "--steps";
        }

        // eBRÁNA pass user IP to allow running webdriver locally
        array_unshift($params, "SSH_CLIENT=".$_SERVER['REMOTE_ADDR']);

        // Build the command to be run.
        return implode(' ', $params);
    }

    /**
     * Given a test type & hash, handle the test run response for the AJAX call.
     *
     * @param  string $type Test type (Unit, Acceptance, Functional)
     * @param  string $hash Hash of the test.
     * @return array  Array of flags used in the JSON respone.
     */
    public function getRunResponse($type, $hash)
    {
        $response = array(
            'message'     => NULL,
            'run'         => FALSE,
            'passed'      => FALSE,
            'state'       => 'error',
            'log'         => NULL
        );

        // If Codeceptions not properly configured, the test won't be found
        // and it won't be run.
        if (! $this->ready())
            $response['message'] = 'The Codeception configuration could not be loaded.';

        // If the test can't be found, we can't run the test.
        if (! $test = $this->getTest($type, $hash))
            $response['message'] = 'The test could not be found.';

        // If there's no error message set yet, it means we're good to go!
        if (is_null($response['message'])) {

            // Run the test!
            $test               = $this->run($test);
            $response['run']    = $test->ran();
            $response['log']    = $test->getLog();
            $response['passed'] = $test->passed();
            $response['state']  = $test->getState();
            $response['title']  = $test->getTitle();
        }

        return $response;
    }

    /**
     * Given a module type & hash, handle the module run response for the AJAX call.
     *
     * @param  string $type Module type (Unit, Acceptance, Functional)
     * @param  string $hash Hash of the module.
     * @return array  Array of flags used in the JSON respone.
     */
    public function getModuleRunResponse($type, $hash)
    {
        $response = array(
            'message'     => NULL,
            'run'         => FALSE,
            'passed'      => FALSE,
            'state'       => 'error',
            'log'         => NULL
        );

        // If Codeceptions not properly configured, the test won't be found
        // and it won't be run.
        if (! $this->ready())
            $response['message'] = 'The Codeception configuration could not be loaded.';

        // If the test can't be found, we can't run the test.
        if (! $module = $this->getModule($type, $hash))
            $response['message'] = 'The module could not be found.';

        // If there's no error message set yet, it means we're good to go!
        if (is_null($response['message'])) {

            // Run the test!
            $module             = $this->runModule($module);
            $response['run']    = $module->ran();
            $response['log']    = $module->getLog();
            $response['passed'] = $module->passed();
            $response['state']  = $module->getState();
            $response['title']  = $module->getName();
        }

        return $response;
    }

    /**
     * Given a group ID, handle the group run response for the AJAX call.
     *
     * @param  string $groupId Group ID
     * @return array  Array of flags used in the JSON respone.
     */
    public function getGroupRunResponse($groupId)
    {
        $response = array(
            'message'     => NULL,
            'run'         => FALSE,
            'passed'      => FALSE,
            'state'       => 'error',
            'log'         => NULL
        );

        // If Codeceptions not properly configured, the test won't be found
        // and it won't be run.
        if (! $this->ready())
            $response['message'] = 'The Codeception configuration could not be loaded.';

        // If the test can't be found, we can't run the test.
        if (! $group = $this->getGroup($groupId))
            $response['message'] = 'The group could not be found.';

        // If there's no error message set yet, it means we're good to go!
        if (is_null($response['message'])) {

            // Run the test!
            $group              = $this->runGroup($group);
            $response['run']    = $group->ran();
            $response['log']    = $group->getLog();
            $response['passed'] = $group->passed();
            $response['state']  = $group->getState();
            $response['title']  = $group->getName();
        }

        return $response;
    }

    /**
     * Check if the Codeception Log Path is writeable.
     *
     * @param string Path that Codeception writes to log.
     * @param string Config location for Codeception.
     * @return array Array of flags used in the JSON respone.
     */
    public function checkWriteable($path=null, $config)
    {
        $response             = array();
        $response['resource'] = $path;

        // Set this to ensure the developer knows there $path was set.
        $response['config']   = ($config);

        if (is_null($path)) {
            $response['error'] = 'The Codeception Log is not set. Is the Codeception configuration set up?';
        } elseif (! file_exists($path)) {
            $response['error'] = 'The Codeception Log directory does not exist. Please check the following path exists:';
        } elseif (! is_writeable($path)) {
            $response['error'] = 'The Codeception Log directory can not be written to yet. Please check the following path has \'chmod 777\' set:';
        }

        $response['ready'] = ! isset($response['error']);

        return $response;
    }

    /**
     * Check that the Codeception executable exists and is runnable.
     *
     * @param  string $file   File name of the Codeception executable.
     * @param  string $config Full path of the config of where the $file was defined.
     * @return array  Array of flags used in the JSON respone.
     */
    public function checkExecutable($file, $config)
    {
        $response             = array();
        $response['resource'] = $file;

        // Set this to ensure the developer knows there $file was set.
        $response['config']   = realpath($config);

        if (! file_exists($file)) {
            $response['error'] = 'The Codeception executable could not be found.';
        } elseif ( ! is_executable($file) && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $response['error'] = 'Codeception isn\'t executable. Have you set executable rights to the following (try chmod o+x).';
        }

        // If there wasn't an error, then it's good!
        $response['ready'] = ! isset($response['error']);

        return $response;
    }
}
