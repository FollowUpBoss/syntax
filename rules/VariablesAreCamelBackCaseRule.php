<?php

namespace spriebsch\PHPca\Rule;

/**
* Make sure that every class definition is followed by an empty line.
*/
class VariablesAreCamelBackCaseRule extends Rule {

    /**
    * Performs the rule check.
    *
    * @returns null
    */
    protected function doCheck() {
        while ($this->file->seekTokenId(T_VARIABLE)) {
            $current = $this->file->current();
            $text = $current->getText();

            $exclude = array('$GLOBALS', '$_SERVER', '$_GET', '$_POST', '$_FILES', '$_REQUEST',
                '$_SESSION', '$_ENV', '$_COOKIE', '$HTTP_RAW_POST_DATA');

            if (!in_array($text, $exclude, true)) {
                if (!preg_match('/^\$+_?[a-z][A-Za-z0-9]*$/', $text)) {
                    $this->addViolation("Variable not camelBack case: {$text}", $current);
                }
            }

            $this->file->next();
        }
    }
}

?>