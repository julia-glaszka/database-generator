### Skrypt stworzony do generowania baz danych MySQL i wypełniania ich danymi.
#### Wspiera typy kolumn:
-  AUTO_INCREMENT
-  POINT
- DATETIME
- TIMESTAMP
- INT
- YEAR
- DECIMAL
- FLOAT
- DOUBLE
- VARCHAR
- TEXT
- DATE
- TIME

## Opis
- Skrypt iteracyjnie wywołuje insert - wiem, na pewno da się lepiej, ale ten projekt powstał w jeden wieczór.
- Można spokojnie wygenerować ok 1mln rekordów z wieloma różnymi polami.
- Ma pełno podatności, kłopoty optymalizacyjne i brak autoryzacji. Przechowyuje hasło do usera db w stringu. Nie używaj tego do poważnych projektów, bardziej jako testowa duża baza danych.
- w metodzie run() zakomentowane są  różne wywołania metody generateAndInsertMultipleData(int), przyjmuje ona ilość rekordów jakie mają zostać dodane w jednym insercie.
## Przykład #1
generateAndInsertMultipleData(1) generuje:
```SQL 
INSERT INTO table_name (column1, column2, column3, ...) VALUES (value1, value2, value3, ...);
```
Baza ma bottlenecki i się przypycha przy takim podejsciu. Zwiększenie liczby dodanych rekordów w jednym insercie przyspiesza wypełnianie bazy, ale do pewnego momentu. Z tego co pamiętam, najoptymalniejsze (dla mojego laptopa), jest generateAndInsertMultipleData(4000).

## Przykład #2
generateAndInsertMultipleData(5) generuje:

```SQL 
INSERT INTO table_name (column1, column2, column3, ...) VALUES (value1, value2, value3, ...), (value1, value2, value3, ...), (value1, value2, value3, ...), (value1, value2, value3, ...), (value1, value2, value3, ...);
```



 ### Parametry DataGenerator i przykładowe wartości
#### Adres serwera
$SERVERNAME = 'localhost'
#### Nazwa użytkownika
$USERNAME = 'root'
#### Hasło
$PASSWORD = ''
#### Nazwa bazy danych
$DBNAME = "baza"
#### Ilość rekordów do wygenerowania
$AMOUNT = 100
#### Nazwa tabeli
$TABLENAME = 'tabela'
#### Schemat tabeli
Jest to tablica przyjmująca zbiór tablic z nazwą kolumny i typem.
$SCHEME = []

### Przykład użycia　

```php

 $datax = array(
     ["id", "INT AUTO_INCREMENT PRIMARY KEY"],
    ["name", "VARCHAR(30)"],
    ["type", "VARCHAR(30)"],
    ["category", "DATE"],
    ["prod_year", "YEAR"],
    ["value", "DOUBLE"],
    ["sale","FLOAT"],
    ["mytime", "TIME"],
    ["location", "POINT"],
    ["delivery_time","TIME"]
 );

$dbx = new DataGenerator('localhost', 'root', '', 'baza', 12000, 'tabela', $datax);
$dbx->run();

```
