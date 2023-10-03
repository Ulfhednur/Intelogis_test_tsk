<?php
namespace Lib;

class Filter
{
    private array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __get($attribute): mixed
    {
        return $this->get($attribute);
    }
    
    public function get($attribute, $default = null, $filter = 'raw'){
        if(isset($this->attributes[$attribute])){
            return match(strtolower($filter)){
                'int', 'integer' => $this->getInt($attribute, $default),
                'cmd' => $this->getCmd($attribute, $default),
                'string', 'str' => $this->getString($attribute, $default),
                'double', 'float' =>  $this->getFloat($attribute, $default),
                'bool', 'boolean' => $this->getBool($attribute, $default),
                'safehtml' => $this->getSafeHtml($attribute, $default),
                default =>  $this->attributes[$attribute]
            };
        }
        return $default;
    }

    public function asArray(): array
    {
        return $this->attributes;
    }

    public function getArray($attribute, $default = [], $itemsFilter = 'raw'): array
    {
        if(isset($this->attributes[$attribute])){
            if(!is_array($this->attributes[$attribute])) {
                $result = (array) $this->attributes[$attribute];
            } else {
                $result = $this->attributes[$attribute];
            }

            if($itemsFilter != 'raw') {
                $result = array_map([__CLASS__, match(strtolower($itemsFilter)){
                    'int', 'integer' => 'filterInt',
                    'cmd' => 'filterCmd',
                    'string', 'str' => 'filterString',
                    'double', 'float' =>  'filterFloat',
                    'bool', 'boolean' => 'filterBool',
                    'safehtml' => 'filterSafeHtml',
                }], $result);
            }

            return $result;
        }
        return $default;
    }

    public function getFloat(string $attribute, ?float $default = null): float
    {
        if(isset($this->attributes[$attribute])){
            return self::filterFloat($this->attributes[$attribute]);
        }
        return $default;
    }

    public function getBool(string $attribute, ?bool $default = null): ?bool
    {
        if(isset($this->attributes[$attribute])){
            return self::filterBool($this->attributes[$attribute]);
        }
        return (bool)$default;
    }

    public function getInt($attribute, ?int $default = null): ?int
    {
        if(isset($this->attributes[$attribute])){
            return self::filterInt($this->attributes[$attribute]);
        }
        return (int)$default;
    }

    public function getCmd($attribute, $default = null): ?string
    {
        if(isset($this->attributes[$attribute])){
            return self::filterCmd($this->attributes[$attribute]);
        }
        return $default;
    }

    public function getString($attribute, $default = null): ?string
    {
        if(isset($this->attributes[$attribute])){
            return self::filterString($this->attributes[$attribute]);
        }
        return $default;
    }

    public function getSafeHtml($attribute, $default = null): ?string
    {
        if(isset($this->attributes[$attribute])){
           return self::filterSafeHtml($this->attributes[$attribute]);
        }
        return (string)$default;
    }

    protected static function filterFloat($value): ?float
    {
        $pattern = '/[-+]?[0-9]+(\.[0-9]+)?([eE][-+]?[0-9]+)?/';
        if (is_array($value)){
            $result = array();
            foreach($value as $eachString){
                preg_match($pattern, (string) $eachString, $matches);
                $result[] = isset($matches[0]) ? (float) $matches[0] : 0;
            }
        } else {
            preg_match($pattern, (string) $value, $matches);
            $result = isset($matches[0]) ? (float) $matches[0] : 0;
        }
        return $result;
    }

    protected static function filterBool($value): bool
    {
        if(is_array($value)){
            $result = array();
            foreach ($value as $eachString){
                $result[] = (bool) $eachString;
            }
        } else {
            $result = (bool) $value;
        }
        return $result;
    }

    protected static function filterInt($value): int
    {
        $pattern = '/[-+]?[0-9]+/';
        if (is_array($value)){
            $result = array();
            foreach($value as $eachString){
                preg_match($pattern, (string) $eachString, $matches);
                $result[] = isset($matches[0]) ? (int) $matches[0] : 0;
            }
        } else {
            preg_match($pattern, (string) $value, $matches);
            $result = isset($matches[0]) ? (int) $matches[0] : 0;
        }
        return $result;
    }

    protected static function filterCmd($value): string
    {
        $pattern = '/[^A-Z0-9_\.-]/i';
        if (is_array($value)){
            $result = array();
            foreach ($value as $eachString){
                $cleaned  = (string) preg_replace($pattern, '', $eachString);
                $result[] = ltrim($cleaned, '.');
            }
        } else {
            $result = (string) preg_replace($pattern, '', $value);
            $result = ltrim($result, '.');
        }
        return $result;
    }

    protected static function filterString($value): string
    {
        if(is_array($value)){
            $result = array();
            foreach($value as $eachString){
                $result[] = strip_tags((string) $eachString);
            }
        } else {
            $result = strip_tags((string) $value);
        }
        return $result;
    }

    protected static function filterSafeHtml($value): string
    {
        return strip_tags($value, '<p><a><b><i><span><strong><em><img><blockquote><br><div><ul><ol><li>');
    }
}