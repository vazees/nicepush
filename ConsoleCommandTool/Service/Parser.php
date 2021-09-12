<?php

namespace ConsoleCommandTool\Service;

use ConsoleCommandTool\Entity;

class Parser {

    private const START_DELIMETER_ARG = '{';
    private const END_DELIMETER_ARG = '}';
    private const START_DELIMETER_OPT = '[';
    private const END_DELIMETER_OPT = ']';
    private const MID_DELIMETER_OPT = '=';

    /**
     * @param string[] $argvParams
     * @return array
     */
    public function parse(array $argvParams): array {
        $result = [];
        $optionValuesByNames = [];
        foreach ($argvParams as $argvParam) {
            if ($this->_isArgument($argvParam)) {
                $result = array_merge($result, $this->_parseArgument($argvParam));
            } else if ($this->_isOption($argvParam)) {
                $optionValuesByNames = $this->_parseOption($argvParam, $optionValuesByNames);
            }
        }
        foreach ($optionValuesByNames as $optionName => $optionValues) {
            $result[] = new Entity\Option($optionName, $optionValues);
        }
        return $result;
    }

    /**
     * @param string $argument
     * @return Entity\Argument[]
     */
    private function _parseArgument(string $argument): array {
        return array_map(fn(string $arg) => new Entity\Argument($arg), explode(',', trim($argument, self::START_DELIMETER_ARG . self::END_DELIMETER_ARG)));
    }

    private function _parseOption(string $argvParam, array $optionValuesByNames): array {
        $option = explode(self::MID_DELIMETER_OPT, trim($argvParam, self::START_DELIMETER_OPT . self::END_DELIMETER_OPT));
        $optionName = $option[0];
        $optionValue = $option[1];
        if (array_key_exists($optionName, $optionValuesByNames)) {
            $optionValuesByNames[$optionName][] = $option[1];
        } else {
            $optionValuesByNames = array_merge($optionValuesByNames, [$optionName => [$optionValue]]);
        }
        return $optionValuesByNames;
    }

    private function _isArgument(string $param): bool {
        return $this->_isStringBetween($param, self::START_DELIMETER_ARG, self::END_DELIMETER_ARG) || strpos($param, self::MID_DELIMETER_OPT) === false;
    }

    private function _isOption(string $param): bool {
        return $this->_isStringBetween($param, self::START_DELIMETER_ARG, self::END_DELIMETER_ARG) && substr_count($param, self::MID_DELIMETER_OPT) === 1;
    }

    private function _isStringBetween(string $haystack, string $startStr, string $endStr): bool {
        return strpos($haystack, $startStr) === 0 && strpos($haystack, $endStr) === strlen($haystack) - 1;
    }

}