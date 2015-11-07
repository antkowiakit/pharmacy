## Apteki - Pharmacy
![header](https://pharmacy.antkowiak.it/img/git_header.png)
Strona domowa projektu: https://pharmacy.antkowiak.it/

## Członkowie zespołu i podział pracy:
- **Patryk Antkowiak** 113983 patryk.antkowiak@student.put.poznan.pl *Backend*
- **Norbert Sroczyński** 113982 norbert.sroczynski@student.put.poznan.pl *iOS Developer*

## Opis projektu:
Projekt Apteki, to wyszukiwarka aptek w pobliżu na urządzenia iOS. Apteki wyświetlane są na mapie. Po kliknięciu ukazuje się odległość do wybranej apteki oraz jest możliwość wyznaczenia trasy do wybranej apteki.

## Założona funkcionalność na pierwszym etapie (6.12.2015) oraz wersji końcowej
**Backend:**
- [x] API do pobierania aptek w wybranej odległości od podanego punktu (lat,lng).
- [x] Możliwość aktualizacji aptek poprzez plik CSV.
- [x] Strona domowa projektu https://pharmacy.antkowiak.it/
- [x] Dokumentacja do API https://pharmacy.antkowiak.it/api/doc

**Aplikacja IOS:**
- [x] Pobieranie aptek z API
- [x] Wyświetlanie pobranych aptek na mapie
- [x] Pobieranie oraz wyświetlanie kolejnych porcji aptek przy przesuwaniu mapy
- [x] Możliwość wyznaczenia trasy do danej apteki

## Opis architektury

Aplikacja iOS pobiera dane z serwera za pośrednictwem API. Serwer odbiera zapytanie z aplikacji mobilej, odpytuję bazę danych, i zwraca w odpowiedzi JSON z aptekami.
![diagram](https://pharmacy.antkowiak.it/img/diagram.png)

## Środowisko realizacji projektu

**iOS:**
- Swift
- Alamofire *(biblioteka do połączeń HTTP)*
- SwiftyJSON *(biblioteka do parsowania danych w formacie JSON)*
- SMCalloutView *(wyświetlanie adnotacji na mapie)*


**Backend:**
- PHP
- Symfony 2
- doctrine/doctrine-migrations-bundle *(migracje bazy danych)*
- jms/serializer-bundle *(serializacja obiektów)*
- friendsofsymfony/rest-bundle *(tworzenie REST API)*
- nelmio/api-doc-bundle *(tworzenie dokumentacji do API)*
- beberlei/DoctrineExtensions *(dodanie do ORM funkcji matematycznych)*