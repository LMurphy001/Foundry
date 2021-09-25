<?php
declare(strict_types=1);
namespace LM\Foundry;
/**
 * Results is a helper class so that functions and methods can return multiple values.
 * Results has three values in it: Info, Errors, Warnings. Each is a string.
 *
 * These 3 strings can be access with getter and setter methods of Results.
 * The reset method clears all three strings.
 * The hasError, hasWarning, and hasInfo  methods indicate if there's an error, warning, or info respectively.
 * Also implements magic method __toString()
 */
class Results {
    private string $error = '';
    private string $warning = '';
    private string $info = '';

    private function getProp(string $propName):string {
        return $this->$propName;
    }
    private function setProp(string $propName, string $newVal ) : string {
        $this->$propName = $newVal;
        return $this->$propName;
    }
    private function addToProp(string $propName, string $toAdd ) : string {
        $this->$propName .= $toAdd;
        return $this->$propName;
    }

    function getError() : string { return $this->getProp('error'); }
    function setError(string $newVal) : string { return $this->setProp('error', $newVal); }
    function addToError(string $toAdd) : string {
        return $this->addToProp('error', $toAdd);
    }

    function getWarning() : string { return $this->getProp('warning'); }
    function setWarning(string $newVal) : string { return $this->setProp('warning', $newVal); }
    function addToWarning(string $toAdd) : string {
        return $this->addToProp('warning', $toAdd);
    }

    function getInfo() : string { return $this->getProp('info'); }
    function setInfo(string $newVal) : string { return $this->setProp('info', $newVal); }
    function addToInfo(string $toAdd) : string {
        return $this->addToProp('info', $toAdd);
    }

    function reset() : void {
        $this->setError('');
        $this->setWarning('');
        $this->setInfo('');
    }

    function hasError() : bool {
        return strlen($this->getError()) > 0;
    }
    function hasWarning() : bool {
        return strlen($this->getWarning()) > 0;
    }
    function hasInfo() : bool {
        return strlen($this->getInfo()) > 0;
    }
    function __toString() : string {
        $str = '';
        if ($this->hasError()) {
            $str .= $this->getError()."\n";
        }
        if ($this->hasWarning()) {
            $str .= $this->getWarning()."\n";
        }
        if ( strlen($this->getInfo() ) > 0) {
            $str .= $this->getInfo();
        }
        return $str;
    }
}
