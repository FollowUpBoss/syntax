<?php

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../VariablesAreCamelBackCaseRule.php';

/**
* Tests for the VariablesAreCamelBackCaseRule rule.
*/
class VariablesAreCamelBackCaseRuleTest extends AbstractRuleTest {

    /**
    * @covers \spriebsch\PHPca\Rule\VariablesAreCamelBackCaseRule
    */
    public function testCorrect() {
        $this->init(__DIR__ . '/../testdata/VariablesAreCamelBackCaseRule/correct.php');

        $rule = new VariablesAreCamelBackCaseRule();
        $rule->check($this->file, $this->result);
        $this->assertEquals(0, $this->result->getNumberOfViolations());
    }

    /**
    * @covers \spriebsch\PHPca\Rule\VariablesAreCamelBackCaseRule
    */
    public function testNoSpaceAtBeginning() {
        $this->init(__DIR__ . '/../testdata/VariablesAreCamelBackCaseRule/incorrect.php');

        $rule = new VariablesAreCamelBackCaseRule();
        $rule->check($this->file, $this->result);

        $this->assertEquals(5, $this->result->getNumberOfViolations());
    }
}

?>