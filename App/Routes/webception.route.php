<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
|--------------------------------------------------------------------------
| Route: Dashboard
|--------------------------------------------------------------------------
|
| The dashboard is the homepage of Webception. It loads all the
| configuration and shows what tests are available to run.
|
*/

$app->get('/', function ($site = null) use ($app) {
    if ($app->request->params('hash')) {
        $tests = FALSE;
        $test_count = 0;
        $webception = $app->config('webception');
        $codeception = $app->codeception;
        $environments = array();

        if ($codeception->ready()) {
            $tests = $codeception->getTests();
            $test_count = $codeception->getTestTally();
            if (isset($codeception->config['env'])) {
                $environments = $codeception->config['env'];
            }
        }

        $app->render('dashboard.html', array(
            'name' => $app->getName(),
            'webception' => $webception,
            'codeception' => $codeception,
            'tests' => $tests,
            'test_count' => $test_count,
            'environments' => $environments
        ));
    } else {
        $modules       = FALSE;
        $webception  = $app->config('webception');
        $codeception = $app->codeception;
        $environments = array();

        if ($codeception->ready()) {
            $modules    = $codeception->getModules();
            if (isset($codeception->config['env'])) {
                $environments = $codeception->config['env'];
            }
        }

        $app->render('modules.html', array(
            'name'        => $app->getName(),
            'webception'  => $webception,
            'codeception' => $codeception,
            'module_environments'       => $modules,
            'modules_count'  => count($modules),
            'environments'=> $environments
        ));
    }
});
