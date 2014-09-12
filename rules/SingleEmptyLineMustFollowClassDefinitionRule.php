<?php

namespace spriebsch\PHPca\Rule;

/**
* Make sure that every class definition is followed by an empty line.
*/
class SingleEmptyLineMustFollowClassDefinitionRule extends Rule {

    /**
    * Performs the rule check.
    *
    * @returns null
    */
    protected function doCheck() {
        while ($this->file->seekTokenId(T_CLASS)) {
            $classToken = $this->file->current();

            $this->file->seekTokenId(T_OPEN_CURLY);
            $openCurlyToken = $this->file->current();

            do {
                $this->file->next();
                $current = $this->file->current();
            } while ($current && $current->getId() == T_WHITESPACE);

            if ($openCurlyToken->getLine() + 2 != $current->getLine()) {
                $this->addViolation("Class definition is not followed by a single empty line", $classToken);
            }

            $this->file->seekToken($classToken);
            $this->file->next();
        }
    }
}

?>