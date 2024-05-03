<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Domain\Serializer;

trait NormalizeTrait
{
    /**
     * Normalize the object.
     *
     * @return array<string, mixed>
     */
    public function normalize(): array
    {
        $class = (new \ReflectionClass($this))->getName();
        $data = [
            '__class__' => $class,
        ];
        foreach (get_object_vars($this) as $key => $value) {
            if (is_object($value) && method_exists($value, 'normalize')) {
                $data[$key] = $value->normalize();
            } else {
                $data[$key] = var_export($value, true);
            }
        }

        return $data;
    }

    /**
     * Denormalize the object.
     *
     * @param array<string, mixed> $data
     */
    public function denormalize(array $data): self
    {
        $class = $data['__class__'];
        unset($data['__class__']);

        if (!class_exists($class)) {
            throw new \RuntimeException('Class does not exist.');
        }

        if ($class !== get_class($this)) {
            throw new \RuntimeException('Class does not match.');
        }

        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value['__class__'])) {
                $class = $value['__class__'];
                $object = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
                if (!method_exists($object, 'denormalize')) {
                    throw new \RuntimeException('Method denormalize does not exist.');
                }
                $this->$key = $object->denormalize($value);
            } else {
                $this->$key = eval('return '.$value.';');
            }
        }

        return $this;
    }
}
