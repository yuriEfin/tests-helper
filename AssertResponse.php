<?php

namespace App\Tests\Base;

trait AssertResponse
{
    public static $requiredKeys = [
        'status' => 'boolean',
        'errors' => 'isNull',
        'requestId' => 'issetKey',
    ];
    
    public static $expression = [
        'isNullable',
        'notNullable',
        'issetKey',
        'notEmpty',
        'isString',
        'isInteger',
        'isFloat',
        'isBoolean',
        'isArray',
    ];
    
    public static $types = [
        'array',
        'boolean',
        'bool',
        'double',
        'float',
        'integer',
        'int',
        'null',
        'numeric',
        'object',
        'real',
        'resource',
        'string',
        'scalar',
        'callable'
    ];
    
    public function customAssertContent(array $actualData, array $expectedData, $isRequiredKey = true, $requiredKeys = [])
    {
        if (!empty($requiredKeys)) {
            self::$requiredKeys = $requiredKeys;
        }
        self::assertInternalType('array', $actualData);
        if ($actualData) {
            if ($isRequiredKey) {
                $this->assertRequireKeys($actualData);
            }
            foreach ($expectedData as $key => $value) {
                if (!is_array($value)) {
                    if ($this->isExpression($value)) {
                        $this->evaluateExpression($actualData, $key, $value);
                    } else {
                        $this->issetKey($actualData, $key);
                        if (in_array($value, self::$types, true)) {
                            self::assertInternalType(
                                $value,
                                $actualData[$key],
                                'Type error value. Asserted: $key {' . $key . '}, type:{' . $value . '} '
                            );
                        } else {
                            // @todo remove after tests
                            echo 'Value: ' . $value . ' for {$key:' . $key . '} is not in list types';
                        }
                    }
                } else {
                    if (isset($actualData[$key]) && isset($expectedData[$key])) {
                        $this->customAssertContent($actualData[$key], $expectedData[$key], false);
                    }
                }
            }
        }
    }
    
    public function evaluateExpression(array $actualData, string $key, string $value)
    {
        if (method_exists($this, $value)) {
            self::$value($actualData, $key);
        }
    }
    
    public function assertRequireKeys(array $actualData)
    {
        foreach (static::$requiredKeys as $key => $value) {
            $this->issetKey($actualData, $key);
        }
    }
    
    public function isExpression($value)
    {
        return in_array($value, self::$expression);
    }
    
    public static function isNullable($actualResponse, $key)
    {
        self::assertNull($actualResponse[$key], 'isNullable: {' . $key . '} required isNullable');
    }
    
    public function notNullable($actualResponse, $key)
    {
        self::assertNotNull(!is_null($actualResponse[$key]), 'notNullable: {' . $key . '} required notNullable');
    }
    
    public function issetKey($actualResponse, $key)
    {
        self::assertTrue(in_array($key, array_keys($actualResponse)), 'issetKey: {' . $key . '} required ISSET');
    }
    
    public function notEmpty($actualResponse, $key)
    {
        $this->issetKey($actualResponse, $key);
        self::assertNotEmpty($actualResponse[$key], 'notEmpty: {' . $key . '} required Not empty');
        self::assertNotNull($actualResponse[$key], 'notEmpty: {' . $key . '} required Not null');
    }
    
    public function isString($actualResponse, $key)
    {
        self::assertInternalType('string', $actualResponse[$key], 'isString: {' . $key . '} required isString');
    }
    
    public function isInteger($actualResponse, $key)
    {
        self::assertInternalType('integer', $actualResponse[$key], 'isInteger: {' . $key . '} required isInteger');
    }
    
    public function isFloat($actualResponse, $key)
    {
        self::assertInternalType('float', $actualResponse[$key], 'isFloat: {' . $key . '} required isFloat');
    }
    
    public function isBoolean($actualResponse, $key)
    {
        self::assertInternalType('boolean', $actualResponse[$key], 'isBoolean: {' . $key . '} required isBoolean');
    }
    
    public function isArray($actualResponse, $key)
    {
        self::assertInternalType('array', $actualResponse[$key], 'isArray: {' . $key . '} required isArray');
    }
}


