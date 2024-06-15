# Matcher

1. translacja atrybutów z Symfony Serializera / JMS Serializera na atrybuty z Ultra Mapper Serializer
2. sprawdzenie blueprintów pod kątem wystąpienia pętli i zasugerowanie stosowania atrybutu `MaxDepth`, jeśli pętla zostanie wykryta
3. iterowanie rekurencyjne po właściwościach:
    1. Wyrzucanie właściwości niezgodnych z grupą `Groups` lub z atrybutem `Ignore`.
    2. matchowanie właściwości po nazwach i atrybucie `TargetProperty`
    3. sprawdzanie typów właściwości, sprawdzenie czy się matchują, jeśli nie, to exception. Jeśli tak to ekstra. Jeśli są to klasy nie będące prostymi obiektami `SimpleObject`, to iterowanie po ich właściwościach
4. wstępne matchowanie klas z typów. Decyduje zagnieżdżenie z `TargetProperty`. Jeśli klas jest wiele to określany jest procent podobieństwa, gdzie tożsame klasy zamykają temat porównania, a brak tożsamości prowadzi do oparcia się o procentową zgodność i wybierana jest klasa o wyższej zgodności i te są ostatecznie matchowane. W przypadku wielu klas, typu unii, powinien zostać zdefiniowany atrybut `Discriminator`.
