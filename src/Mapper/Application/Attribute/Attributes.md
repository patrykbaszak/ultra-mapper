# Ultra Mapper Attributes

## Accessor

Wskazuje metody w obiekcie, które można wywołać, aby pobrać lub zapisać wartość.

```php
?string $getter = null, # getProperty
?string $setter = null, # setProperty
```

### Efekt

```php
# property assignment
$var = $source->getProperty();
# and/or
$target->setProperty($var);
```

## ApplyToCollectionItems

Pozwala dodać atrybuty do elementów kolekcji

```php
/** @var object[] */
array $attributes = [], # [new Accessor('addProperty')]
```

### Efekt

```php
# property assignment
$collection = [];
foreach ($source['collection'] as $index => $item) {
    $collection[$index] = $this->mapItem($item, $index);
}
$target->collection = $collection;
```

## Callback

Callback to wszechstronny atrybut służący do deklarowania specjalnych akcji przy przypisywaniu danych do właściwości.

