<?php

namespace TH\MAX\DTO;

use ReflectionProperty;
use TH\MAX\Client\DTO\Messages\Collection\MessageCollection;

abstract class BaseDTO implements \JsonSerializable
{
    protected array $_ignored = [];

    public function __construct(array $item = [])
    {
//        if ($item['items'] instanceof MessageCollection) {
//            print_r($item);
//            exit;
//        }
        $this->fromArray($item);
    }

    public function toArray(bool $without_null = true, bool $recursive = false): array
    {
        $items = get_object_vars($this);

        unset($items['_ignored']);

        foreach ($items as $key => $value) {
            if ($without_null && $value === null) {
                unset($items[$key]);
                continue;
            }

            if ($recursive) {
                $items[$key] = $this->toArrayRecursive($value, $without_null, $recursive);
            }
        }

        return $items;
    }

    /**
     * @throws \ReflectionException
     */
    public function fromArray(array $data): self
    {
        foreach ($data as $name => $value) {
            if (property_exists($this, $name)) {
                $reflection = new ReflectionProperty(get_class($this), $name);

                $type = $reflection->getType()->getName();
                if ($this->isPrimitiveType($type)) {
                    $this->$name = $value;
                }
                else {
                    /** @var BaseDTO $class */
                    $class = new $type();
                    $this->$name = $class->fromArray($value);
                }
            }
        }

        return $this;
    }

    private function isPrimitiveType(string $type): bool
    {
        return in_array($type, ['int', 'float', 'bool', 'array', 'string']);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    private function toArrayRecursive($value, bool $without_null, bool $recursive)
    {
        if ($value instanceof self) {
            return $value->toArray($without_null, $recursive);
        }

        if (is_array($value)) {
            return array_map(fn($item) => $this->toArrayRecursive($item, $without_null, $recursive), $value);
        }

        return $value;
    }
}