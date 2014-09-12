<?php
/**
* FollowUpBoss: Real Estate Lead Management Software.
*
* @author        Anthony Gentile <anthony@followupboss.com>
* @copyright     Copyright (c) 2014, Enchant LLC.
* @license       Property of Enchant LLC. All rights reserved.
*/
namespace FUBSyntax;

require dirname(__FILE__) . '/vendor/spriebsch/phpca/src/Autoload.php';

use Exception;
use spriebsch\PHPca\Result;
use spriebsch\PHPca\Application;
use spriebsch\PHPca\Configuration;
use spriebsch\PHPca\ProgressPrinterInterface;

/**
* Runs syntax checks against files. This will validate
* against the Lithium and FUB coding, documentation and testing standards.
*/
class Syntax implements ProgressPrinterInterface {

    /**
    * Enable output of metrics.
    *
    * @var boolean
    */
    public $metrics = false;

    protected $stdio;

    /**
    * Main method.
    *
    * @param string $path Absolute path to file or directory.
    * @return boolean
    */
    public function run($path, $stdio) {
        $this->stdio = $stdio;
        $app = new Application(getcwd());
        $app->registerProgressPrinter($this);

        $file = __DIR__ . '/fub-standard.ini';

        $config = new Configuration(getcwd());
        $config->setStandard(parse_ini_file($file, true));
        $config->setConfiguration(array());

        $php = PHP_BINDIR . '/' . (substr(PHP_OS, 0, 3) == 'WIN' ? 'php.exe' : 'php');

        $begin = microtime(true);

        try {
            $result = $app->run($php, $path, $config);
        } catch (Exception $e) {
            $this->out($message = $e->getMessage());
            return $message == 'No PHP files to analyze';
        }

        if ($this->metrics) {
            $this->_metrics($result, microtime(true) - $begin);
        }
        return !$result->hasErrors();
    }

    public function showProgress($file, Result $result, Application $application) {
        $message = $file;
        $self = $this;

        $format = function ($result) use ($self) {
            return sprintf(
                '%1$4s| %2$3s| %4$s',
                $result->getLine() ?: '??',
                $result->getColumn() ?: '??',
               '',
                $result->getMessage() ?: '??'
            );
        };

        if ($result->wasSkipped($file)) {
            $this->stdio->outln("[<<blue>>skip<<reset>>] {$message}");
        } elseif ($result->hasLintError($file)) {
            $this->stdio->outln("[<<blue>>exception<<reset>>] {$message}");
            $this->stdio->outln($format($result->getLintError($file)));
        } elseif ($result->hasRuleError($file)) {
            $this->stdio->outln("[<<blue>>exception<<reset>>] {$message}");
            foreach ($result->getRuleErrors($file) as $error) {
                $this->stdio->outln($format($error));
            }
        } elseif ($result->hasViolations($file)) {
            $this->stdio->outln("[<<red>>fail<<reset>>] {$message}");

            foreach ($result->getViolations($file) as $violation) {
                $this->stdio->outln($format($violation));
            }
        } else {
            $this->stdio->outln("[<<green>>pass<<reset>>] {$message}");
        }
    }
    protected function _metrics($result, $took) {
        $this->stdio->outln(PHP_EOL);
        $this->stdio->outln('Metrics');
        $this->stdio->outln(PHP_EOL);
        $this->stdio->outln(sprintf("Took: %.2ds", $took));
        $this->stdio->outln(PHP_EOL);
        $this->stdio->outln('Files: ' . $result->getNumberOfFiles());
        $this->stdio->outln('Skipped: ' . $result->getNumberOfSkippedFiles());
        $this->stdio->outln(PHP_EOL);
        $this->stdio->outln('Lint errors: ' . $result->getNumberOfLintErrors());
        $this->stdio->outln('Rule errors: ' . $result->getNumberOfRuleErrors());
        $this->stdio->outln('Violations: ' . $result->getNumberOfViolations());
        $this->stdio->outln(PHP_EOL);
    }
}

?>