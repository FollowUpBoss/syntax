<?php

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../SingleEmptyLineMustFollowClassDefinitionRule.php';

/**
* Tests for the SingleEmptyLineMustFollowClassDefinitionRule rule.
*/
class SingleEmptyLineMustFollowClassDefinitionRuleTest extends AbstractRuleTest {

    /**
    * @covers \spriebsch\PHPca\Rule\SingleEmptyLineMustFollowClassDefinitionRule
    */
    public function testNoEmptyLine() {
        $this->init(__DIR__ . '/../testdata/SingleEmptyLineMustFollowClassDefinitionRule/noemptyline.php');

        $rule = new SingleEmptyLineMustFollowClassDefinitionRule();
        $rule->check($this->file, $this->result);

        $this->assertEquals(1, $this->result->getNumberOfViolations());
    }

    /**
    * @covers \spriebsch\PHPca\Rule\SingleEmptyLineMustFollowClassDefinitionRule
    */
    public function testSingleEmptyLine() {
        $this->init(__DIR__ . '/../testdata/SingleEmptyLineMustFollowClassDefinitionRule/singleemptyline.php');

        $rule = new SingleEmptyLineMustFollowClassDefinitionRule();
        $rule->check($this->file, $this->result);

        $this->assertEquals(0, $this->result->getNumberOfViolations());
    }

    /**
    * @covers \spriebsch\PHPca\Rule\SingleEmptyLineMustFollowClassDefinitionRule
    */
    public function testTwoEmptyLines() {
        $this->init(__DIR__ . '/../testdata/SingleEmptyLineMustFollowClassDefinitionRule/twoemptylines.php');

        $rule = new SingleEmptyLineMustFollowClassDefinitionRule();
        $rule->check($this->file, $this->result);

        $this->assertEquals(1, $this->result->getNumberOfViolations());
    }
}

?>