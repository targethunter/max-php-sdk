<?php

namespace TH\MAX\DTO;

abstract class BaseDTO implements \JsonSerializable
{
    protected array $_ignored = [];

    public function __construct(array $item = [])
    {
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

    public function fromArray(array $data): self
    {
        foreach ($data as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }

        return $this;
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