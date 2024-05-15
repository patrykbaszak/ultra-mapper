# Ultra Mapper - Manifest

The ultra mapper have to process from any:

- flat array
- array
- anonymous object
- class object (by *reflection class*/*constructor*/*public properties*/*getters and setters*)

to any

- flat array
- array
- anonymous object
- class object (by *reflection class*/*constructor*/*public properties*/*getters and setters*)

### Supported data types

each can be combined with any of them

- 
    ```php
    # null
    null;
    ```
- 
    ```php
    # boolean
    true;
    false;
    ```
- 
    ```php
    # int
    0;
    1;
    2;
    # {...}
    ```
- 
    ```php
    # string
    "some string";
    ```
- 
    ```php
    # array
    [
        "a" => ["b" => "c"]
    ];

    # flat array
    [
        "a.b" => "c"
    ]
    ```
- 
    ```php
    # object
    (object) [
        0 => "a",
        1 => "b",
        2 => "c",
        # {...}
    ];

    (object) [
        "a" => "b"
    ];
    ```
- 
    ```php
    # class object which wrappes another data type
    new DateTime($datetime);
    new ArrayObject($arr);
    ```
- 
    ```php
    # class object
    class Test
    {
        private string $name;
    }
    ```
- 
    ```php
    # collection of any data type
    [
        new Test(),
        new Test()
    ];
    
    [
        "a", "b", "c" #, {...}
    ];
    ```

### Placeholders used to build mappers

Dane są obsługiwane w ramach 4 zmiennych: źródła, elementu kolekcji, celu oraz zmiennej zastępczej. Ta ostatnia jest opcjonalna i może zostać wykorzystana gdy niezbędny jest do wykonania callback na przypisywanej wartości.

| name | includes | example | description |
| - | - | - | - |
| `{{function.name}}` | | `handleData` | Nazwa funkcji wykonującej mapowanie. Taka funkcja przyjmuje dane oraz dowolne inne argumenty, np. indeks w pętli, oraz zwraca dane wyjściowe.
| `{{source.name}}` | | `$data` | Źródło danej wartości
| `{{source.propertyName}}` | | `property` | Nazwa właściwości w źródle
| `{{source.getter}}` | | `{{source.name}}['{{source.propertyName}}']` / `{{collection.callback}}` | Określa sposób pobrania wartości z źródła
| `{{collection.index}}` | | `$item` | Indeks danej w kolekcji
| `{{collection.item}}` | | `$item` | Źródło danej wartości w kolekcji
| `{{collection.callback}}` | | `$this->{{function.name}}({{collection.item}}, {{collection.index}})` | Określa sposób wywołania funkcji callback na elemencie kolekcji
| `{{target.name}}` | | `$output` | Opis zmiennej wyjściowej
| `{{target.init}}` | | `$output = new Test();` | Inicjator zmiennej wyjściowej
| `{{target.propertyName}}` | | `property` | Nazwa właściwości wyjściowej
| `{{target.setter}}` | | `{{target.name}}['{{target.propertyName}}'] = {{source.getter}};` | Określa sposób przypisania wartości wyjściowej
| `{{var.name}}` | | `$var` | Nazwa zmiennej zastępczej
| `{{var.setter}}` | | `$var = {{source.getter}};` | Sposób przypisania zmiennej zastępczej
| `{{var.getter}}` | | `$var` | Sposób pobrania wartości z zmiennej zastępczej
| `{{meta}}` | | `$meta` | Specjalna zmienna przechowująca meta informacje dla wybranej właściwości. Znajdą się tutaj takie dane jak: pełna ścieżka własciwości, indeks w kolekcji i wszystkie inne dane niezbędne w procesie mapowania. Pomysł: przechowywanie wyjątków, żeby potem zwrócić je zbiorczo.

### Function construction

```php
/**
 * The `X765` part is some random string builded on hash of the full classname.
 * Classes `App\Test` and `\AppTest` could has the same function name.
 * 
 * @param array<string, mixed> $source
 * @param Meta $meta includes path to root or anything else required by mapping process
 * 
 * @return App\Test
 */
private function mapAppTestX765(array $source, Meta $meta): Test
{
    static $ref_565f49f2 ??= new ReflectionClass(App\Test::class);
    /** @var App\Test $output */
    $output = $ref_565f49f2->newInstanceWithoutConstructor();

    
    if (array_key_exists('name', $source)) {
        $ref_565f49f2->getProperty('name')->setValue($output, $source['name']);
    }

    return $output;
}
```

### Exceptions

The exception code are started from `5920`

### Mapping process

#### Inicjalizacja

 - z zmiennej `$source`
 - z wartości domyślnej
 - z wywołania funkcji inicjalizującej

#### Przypisanie

 - do zmiennej `$target`
 - do zmiennej `$var`

#### Wywołanie funkcji

 - dowolna akcja na zmiennej `$var`

#### Przypisanie

 - przypisanie zmiennej `$var` do zmiennej `$target`

#### Wywołanie funkcji końcowej

 - dowolna akcja kończąca
