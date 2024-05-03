# Ultra Mapper - Attributes

### **ApplyToCollectionItems**
Stosuje podane atrybuty do elementów kolekcji

### **Callback**
Wykonuje dowolny callback na właściwości. Może to być callback inicjujący. Użycie callback.runtime wymusza zastosowanie zmiennej zamiennej `{{var}}`.

#### Before
Pozwala zainicjować wartość właściwości, albo zwalidować dane wejściowe, np. pod kątem tego czy w ogóle istnieją w źródle.

#### Runtime
Otrzymujesz dostęp do przypisanej wartości z źródła do zmiennej zamiennej `{{var}}` i możesz z tym zrobić co zechcesz

#### After
Jest po to, aby np. zalogować stan albo zrobić cokolwiek innego, np. wywołać wyjątek.

### **Discriminator**
Pozwala określić mapę polegającą na wartości właściwości `type` i docelowej klasy Blueprint. `type` może być określony wewnątrz interesującej nas właściwości źródłowej lub obok.

### **Groups**
Pozwala zdefiniować grupy normalizacji

### **Ignore**
Pozwala zignorować wybraną właściwość w procesie normalizacji

### **MaxDepth**
Pozwala zablokować głębokość mapowania. Dane poniżej granicy będą:

#### Mapowane do prostszego obiektu, który zapobiega rekurencji
należy go wskazać

#### Wybranej właściwości spośród tych dostępnych niżej
w większości przypadków może to być id

#### Konkretnej wartości
np. wiadomości o treści `null` lub `maps to {classname}`

#### Callbacku, który zwróci zawartość


### **SimpleObject**
Np. DateTime, ale należy użyć tego atrybutu na swoich autorskich klasach, aby były obsługiwane jako wrappery do danych.

Atrybut pozwala zdefiniować constructor oraz deconstructor, które będą używane w procesach normalizacji oraz denormalizacji.

### **TargetProperty**
Pozwala określić jak właściwość nazywa się w źródle danych. Można oznaczyć flagę do określenia, czy podana wartość ma zostać użyta w procesie normalizacji.

#### Mapowanie z klasy A do klasy B, jeśli `#[TargetProperty]` jest w klasie A
- A.property => B.targetProperty

#### Mapowanie z klasy A do klasy B, jeśli `#[TargetProperty]` jest w klasie B i nie ma go w klasie A
- A.targetProperty => B.property

#### Mapowanie z klasy A do klasy B, jeśli `#[TargetProperty]` jest w klasie A oraz w klasie B 
`#[TargetProperty]` w klasie wyjściowej jest ignorowane, jeśli `#[TargetProperty]` jest zadeklarowane w źródle.
- A.property => B.targetProperty

#### Normalizacja, jeśli flaga `normalization = true` 
- A.property => [targetProperty]

#### Normalizacja, jeśli flaga `normalization = false`
- A.property => [property] 

#### Denormalizacja, jeśli flaga `denormalization = true`
- [targetProperty] => A.property

#### Denormalizacja, jeśli flaga `denormalization = false`
- [property] => A.property
