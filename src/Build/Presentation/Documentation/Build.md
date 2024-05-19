# Ultra Mapper Build

## Construction

```xml
<source.isExists>
    <try.catch>
        <target.assignment from="source.getter" / from="var.getter" / from="collection.getter">
        </target.assignment>
    </try.catch>
</source.isExists>
<callback.final></callback.final>
```
 
## Option 1 - basic assignment

### mixed

```php
public mixed $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $target->property = $source['property'];
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $this->exceptions['parent.property'] = new NotExistsValueException('parent.property', ...);
}
```

### nullable string
```php
public ?string $property;
```

```php
// include Option 1
// ...
//  try {
        $target->property = $this->mapString(..., $source['property'], true);
//  } catch (\Throwable $e) {
//     ...
```

### boolean

```php
public bool $property;
```

```php
// include Option 1
// ...
//  try {
        $target->property = $this->mapBool(..., $source['property'], false);
//  } catch (\Throwable $e) {
//     ...
```

## Option 2 - assignment with default value

```php
public ?string $property = null;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $target->property = $this->mapString(..., $source['property'], true);
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $target->property = null;
}
```

## Option 3 - assignment with callback  

### initialization

```php
#[Callback('new \DateTime("now")', 0)]
public \DateTime $property;
```

```php
try {
    $target->property = new \DateTime("now");
} catch (\Throwable $e) {
    $this->exceptions['parent.property'] = $e;
}
```

### callback if not exists

```php
#[Callback('$this->logger->log();', 1)]
public string $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $target->property = $this->mapString(..., $source['property'], true);
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $this->logger->log();
}
```

### initialization callback if not exists

```php
#[Callback('Uuid::v4()->__toString()', 2)]
public string $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $target->property = $this->mapString(..., $source['property'], true);
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $target->property = Uuid::v4()->__toString();
}
```

### callback

```php
#[Callback('$var = $var * 2;', 3)]
public int $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $var = $this->mapInt(..., $source['property'], true);
        $var = $var * 2;
        $target->property = $var;
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $this->exceptions['parent.property'] = new NotExistsValueException('parent.property', ...);
}
```

### assignment

```php
#[Callback('$var = {{source.getter}};', 4)]
public int $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $var = $source['property'];
        $target->property = $var;
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $this->exceptions['parent.property'] = new NotExistsValueException('parent.property', ...);
}
```

### callback on failure

```php
#[Callback('throw $e;', 5)]
public mixed $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $target->property = $source['property'];
    } catch (\Throwable $e) {
        throw $e;
    }
} else {
    $this->exceptions['parent.property'] = new NotExistsValueException('parent.property', ...);
}
```

### final callback


```php
#[Callback('// do nothing', 6)]
public mixed $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $target->property = $source['property'];
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $this->exceptions['parent.property'] = new NotExistsValueException('parent.property', ...);
}
// do nothing
```

## Option 4 - collection

```php
/** @var string[] */
public array $property;
```

```php
if (array_key_exists('property', $source)) {
    try {
        $collection = [];
        foreach ($source['property'] as $index => $item) {
            $collection[$index] => $this->mapString(..., $item, false);
        }
        $target->property = $collection;
    } catch (\Throwable $e) {
        $this->exceptions['parent.property'] = $e;
    }
} else {
    $this->exceptions['parent.property'] = new NotExistsValueException('parent.property', ...);
}
```
