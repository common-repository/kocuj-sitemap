=== Kocuj Sitemap ===

Contributors: domko
Tags: sitemap, menus, pages, posts, authors, tags, taxonomies, custom posts types, widget, html 5, shortcode, multilingual
Author URI: http://kocuj.pl
Plugin URI: http://kocujsitemap.wpplugin.kocuj.pl
Requires at least: 5.4
Tested up to: 5.5
Requires PHP: 5.3
Stable tag: 2.7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wyświetla mapę strony dzięki krótkiemu kodowi, widgetowi lub funkcji PHP. Wielojęzyczność (qTranslate X). WP i WP Multisite.

== Description ==

Wtyczka *Kocuj Sitemap* dodaje krótki kod `[KocujSitemap]`, który umieszcza mapę strony w miejscu, w którym zostanie użyty. Pozwala to na wyświetlanie odnośników do wszystkich Twoich wpisów, stron, elementów menu, autorów, tagów i wpisów własnych typów gdziekolwiek na Twojej stronie internetowej - nawet wewnątrz artykułu. Istnieje również funkcja PHP, która pozwala umieścić mapę stronie gdziekolwiek na stronie internetowej. Ponadto jest możliwość użycia widgetu, aby umieścić mapę stronę gdziekolwiek w bocznym pasku.

Niniejsza wtyczka wspiera wielojęzyczne strony internetowe. Jeżeli posiadasz zainstalowaną wtyczkę zgodną z *Kocuj Sitemap* (obecnie jest to *qTranslate X*), wtyczka utworzy mapę strony na Twojej stronie internetowej zgodnie z aktualnie wybranym językiem. Jeżeli nie posiadasz wtyczki, która wspiera wielojęzyczność, wtyczka *Kocuj Sitemap* wyświetli mapę strony dla domyślnego języka zdefiniowanego dla Twojej instalacji WordPressa.

Mapa strony jest automatycznie tworzona i przechowywana w pamięci podręcznej, która jest używana w mapie strony, aby zapobiec używaniu bazy danych przy wczytywaniu Twojej strony internetowej, po każdej zmianie jakiegokolwiek elementu w panelu administracyjnym (np. gdy zmiesz wpis). Ten proces przyspiesza wczytywanie mapy strony na Twojej stronie internetowej.

Wtyczka *Kocuj Sitemap* może być używana w standardowej instalacji WordPress lub w WordPress Multisite.

= Możliwości =

* Zgodność with HTML 5,
* Wsparcie dla stron wielojęzycznych (z wtyczką qTranslate X),
* Szybkie działanie dzięki używaniu pamięci podręcznej,
* Wyświetlanie wpisów, stron, elementów menu, autorów, tagów i wpisów własnych typów (z własnymi taksonomiami),
* Wyświetlanie wszystkich elementów z podziałem na sekcje lub bez podziału,
* Możliwość zmiany kolejności wyświetlanych wpisów,
* Możliwość wykluczania wybranych wpisów,
* Możliwość ukrywania wybranych typów elementów, np. wszystkich wpisów,
* Łatwe dodawanie mapy strony z użyciem krótkiego kodu przy pomocy przycisku z edytorze HTML lub wizualnym,
* Możliwość ustawienia lewego marginesu dla każdego poziomu w wielopoziomowej mapie strony, jeżeli motyw ma problemy z wyświetlaniem takiej listy,
* Wiele opcji konfiguracyjnych,
* Wiele filtrów dla lepszej kontroli wtyczki.

= Wymagania =

Niniejsza wtyczka wymaga PHP 5.x (od 5.3) lub PHP 7.x i WordPressa 4.9 lub nowszą wersję. Wtyczka działa w standardowym środowisku WordPressa oraz w trybie Multisite. Bardzo zalecane jest używanie najnowszej wersji WordPressa. Niniejsza wtyczka używa krótkiego kodu, więc w Gutenbergu (edytorze WordPressa od wersji 5.0) nie będzie działała jako blok, a krótki kod będzie musiał być dodawany ręcznie.

= Jak używać =

Istnieje parę możliwości wyświetlania mapy strony:
* używając krótkiego kodu `[KocujSitemap]` wewnątrz wpisu dowolnego typu,
* wyświetlając widget,
* używając funkcji PHP w dowolnym miejscu w kodzie, np. wewnątrz motywu.

Krótki kod `[KocujSitemap]` posiada opcjonalne parametry:

* `homelinktext` - tekst, który będzie używany jako tekst odnośnika do głównej strony,
* `class` - nazwa arkusza stylów, który będzie dodany do blokowego elementu (`div` lub `nav`) zawierającego całą mapę strony,
* `excludepost` - oddzielona przecinkami lista identyfikatorów wpisów jakiegokolwiek typu (wpisów, stron, wpisów własnych typów) do wykluczenia,
* `excludecategory` - oddzielona przecinkami lista identyfikatorów kategorii wpisów do wykluczenia,
* `excludeauthor` - oddzielona przecinkami lista identyfikatorów autorów do wykluczenia,
* `excludeterm` - oddzielona przecinkami lista identyfikatorów tagów wpisów lub własnych taksonomii do wykluczenia,
* `hidetypes` - oddzielona przecinkami lista typów elementów do ukrycia; może zawierać następujące typy: "author" (aby ukryć autorów), "custom" (aby ukryć wpisy własnych typów), "home" (aby ukryć odnośnik do strony głównej), "menu" (aby ukryć menu), "page" (aby ukryć strony), "post" (aby ukryć wpisy), "tag" (aby ukryć tagi).

Na przykład, jeśli użyjesz `[KocujSitemap homelinktext="NEW LINK TEXT" class="new_class"]` mapa strony będzie wyświetlana w blokowym elemencie (`div` lub `nav`) z klasą CSS `new_class` i odnośnikiem do głównej strony zatytułowanym `NEW LINK TEXT`.

Jeżeli dodasz inne parametry, np. `[KocujSitemap homelinktext="NEW LINK TEXT" class="new_class" excludepost="5,6" excludeterm="12" hidetypes="page"]`, mapa strony będzie wyświetlana w blokowym elemencie (`div` lub `nav`) z klasą CSS `new_class`, z odnośnikiem do głównej strony zatytułowanym `NEW LINK TEXT`, nie będą wyświetlane wpisy jakiegokolwiek typu o identyfikatorach 5 i 6, tagi wpisów i własne taksonomie o identyfikatorze 12 oraz żadne strony nie będą wyświetlane.

Zamiast używać krótkiego kodu, możesz edytować plik PHP odpowiedzialny za motyw. Wtyczka *Kocuj Sitemap* definiuje globalną funkcję PHP, której deklaracja to: `<?php function kocujsitemap_show_sitemap($homeLinkText = '', $class = '', array $exclude = array(), array $hideTypes = array()); ?>`. Parametry `$homeLinkText` i `$class` mają taką samą funkcję jak odpowiadające im parametry w krótkim kodzie `[KocujSitemap]`.

Na przykład, jeżeli użyjesz `<?php kocujsitemap_show_sitemap('NEW LINK TEXT', 'new_class'); ?>`, mapa strony będzie wyświetlana w blokowym elemencie (`div` lub `nav`) z klasą CSS `new_class` i odnośnikiem do głównej strony zatytułowanym `NEW LINK TEXT`.

Więcej informacji należy dodać o parametrze `$exclude`. Powinien on zawierać tablicę identyfikatorów skategoryzowanych (z właściwym identyfikatorem klucza) po typie wpisu do wykluczenia. Następujące typy wpisów są dostępne:

* `post` - do wykluczania wpisów jakiegokolwiek typu (wpisów, stron, wpisów własnych typów),
* `category` - do wykluczania kategorii wpisów,
* `author` - do wykluczania autorów,
* `term` - do wykluczania kategorii wpisów, tagów wpisów i własnych taksonomii.

Na przykład, tablica `$exclude` zawierająca `array('post' => array(5, 6), 'term' => array(12))` wykluczy jakiekolwiek typy wpisów o identyfikatorach 5 i 6 oraz tagi wpisów i własne taksonomie o identyfikatorze 12.

Parametr $hideTypes posiada listę typów elementów to ukrycia. Może zawierać następujące typy: "author" (aby ukryć autorów), "custom" (aby ukryć wpisy własnych typów), "home" (aby ukryć odnośnik do strony głównej), "menu" (aby ukryć menu), "page" (aby ukryć strony), "post" (aby ukryć wpisy), "tag" (aby ukryć tagi).

Na przykład, tablica `$hideTypes` zawierająca `array('post', 'page')` ukryje wszystkie wpisy i strony.

Istnieje również możliwość użycia widgetu. Ma on możliwość wyświetlania mapy strony jako zwykłą lub rozwijaną listę. Ma on również możliwość wykluczania wybranych elementów lub ukrywania wybranych typów elementów z mapy strony.

= Konfiguracja =

W panelu administracyjnym znajduje się opcja *Mapa strony*, która jest używana do konfiguracji zachowania wtyczki *Kocuj Sitemap*. Jeżeli wybierzesz jakiekolwiek podmenu z menu *Mapa strony*, znajdziesz się w miejscu, w którym możesz ustawić opcje przeznaczone dla tej wtyczki.

Ustawienia na każdej stronie administracyjnej są podzielone na zakładki. Możesz aktywować tylko jedną z nich naraz. Zakładka jest wybierana poprzez kliknięcie na jej nazwie.

Każda zakładka zawiera zestaw opcji. Każda opcja posiada opis, który jest wyświetlany, gdy ustawisz nad nią kursor myszy. Możesz również zobaczyć bardziej dokładnie informacje klikając na przycisku *Pomoc* znajdującym się na górze strony.

Zmiany w konfiguracji mogą być zapisane przez kliknięcie na przycisku *Zapisz ustawienia*. Istnieje możliwość odtworzenia ustawień, które zostały ustawione po instalacji wtyczki, poprzez wybranie opcji *Przywróć ustawienia* w menu wtyczki.

Pamiętaj, że usunięcie wtyczki wyczyści wszystki jej opcje zapisane w bazie danych.

= Klasy CSS =

Wtyczka *Kocuj Sitemap* wyświetla mapę strony używając paru klas CSS, więc możesz dostosować jej wygląd do swoich wymagań.

Można użyć jednej z następujących klas CSS:

* `kocujsitemap` - używana przez główny kontener mapy strony (`nav` lub `div`) w krótkim kodzie, funkcji PHP i widgetu,
* `kocujsitemapwidget` - używana przez główny kontener mapy strony (`nav` lub `div`) w widgecie,
* `kocujsitemap-home` - używana przez element `<li>` w mapie strony, gdy odnośnik prowadzi do głównej strony,
* `kocujsitemap-post` - używana przez element `<li>` w mapie strony, gdy odnośnik prowadzi do wpisu dowolnego typu (wpis, strona, wpis własnego typu),
* `kocujsitemap-category` - używana przez element `<li>` w mapie strony, gdy odnośnik prowadzi do kategorii wpisów,
* `kocujsitemap-term` - używana przez element `<li>` w mapie strony, gdy odnośnik prowadzi do kategorii tagów lub własnej taksonomii,
* `kocujsitemap-author` - używana przez element `<li>` w mapie strony, gdy odnośnik prowadzi do autora,
* `kocujsitemap-unknown` - używana przez element `<li>` w mapie strony, gdy odnośnik prowadzi do czegoś innego niż wymienione powyżej elementy (np. gdy jest to odnośnik do innej strony internetowej).

= Planowane zmiany =

* Tworzenie jednej mapy strony dla wszystkich blogów w instalacji wieloblogowej,
* Tworzenie więcej niż jednej mapy strony,
* Łatwa kontrola parametrów krótkiego kodu w edytorze HTML i wizualnym,
* Generowanie mapy strony XML dla wyszukiwarek.

= Kontakt =

Jeżeli masz jakąkolwiek sugestię, możesz użyć formularza kontaktowego pod adresem [http://kocujsitemap.wpplugin.kocuj.pl/contact/](http://kocujsitemap.wpplugin.kocuj.pl/contact/).

Jeżeli chcesz dostawać regularnie informacje o niniejszej wtyczce, zostań jej fanem na Facebooku: [http://www.facebook.com/kocujsitemap](http://www.facebook.com/kocujsitemap)

Zobacz również oficjalną stronę internetową wtyczki: [http://kocujsitemap.wpplugin.kocuj.pl](http://kocujsitemap.wpplugin.kocuj.pl)

== Dla programistów ==

Wtyczka *Kocuj Sitemap* zawiera zestaw filtrów, ktore pozwalają na zmianę niektórych jej zachowań. Pozwala to na przystosowanie wtyczki do wymagań programistycznych innej wtyczki lub motywu bez dokonywania zmian w kodzie wtyczki *Kocuj Sitemap*.

Wtyczka *Kocuj Sitemap* zawiera następujące filtry:

***kocujsitemap_additional_multiple_languages_php_classes***

*Parametry:*

1. tablica (array) - lista dodatkowych klas PHP, które obsługują wielojęzyczne wtyczki i nie są wbudowane we wtyczkę *Kocuj Sitemap*

*Zwracana wartość:*

1. tablica (array) - lista klas PHP, które obsługują wielojęzyczne wtyczki i nie są wbudowane we wtyczkę *Kocuj Sitemap*

*Opis:*

Ten filtr dodaje klasę PHP obsługującą wielojęzyczne strony internetowe. Aby dodać nową klasę PHP, powinieneś dodać nowy element do tablicy, która zawiera następujące pola:

* `filename` - pełna ścieżka do pliku z nową klasą PHP,
* `class` - nazwa klasy PHP.

Więcej informacji o tej funkcjonalności możesz znaleźć zaglądając do pliku *classes/multiple-languages/template-for-developers/some-plugin.class.php*, który może być dobrym punktem startowym do zaprogramowania innej klasy PHP. Pamiętaj, że ta klasa PHP powinna implementować interfejs *\KocujSitemapPlugin\Interfaces\Language*.

***kocujsitemap_default_custom***

*Parametry:*

1. tablica (array) - lista uproszczonych nazw własnych typów wpisów

*Zwracana wartość:*

1. tablica (array) - lista uproszczonych nazw własnych typów wpisów

*Opis:*

Ten filtr ustawia domyślną listę własnych typów wpisów, które są wyświetlanie w mapie strony jeżeli lista "opcje listy wpisów własnych typów" w panelu administracyjnym jest pusta.

***kocujsitemap_default_exclude_author***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów autorów do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów autorów do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę autorów do wykluczenia dla krótkiego kodu `[KocujSitemap]`.

***kocujsitemap_default_exclude_category***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów kategorii wpisów do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów kategorii wpisów do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę kategorii wpisów do wykluczenia dla krótkiego kodu `[KocujSitemap]`.

***kocujsitemap_default_exclude_post***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów wpisów, stron i wpisów własnych typów do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów wpisów, stron i wpisów własnych typów do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę wpisów do wykluczenia dla krótkiego kodu `[KocujSitemap]`.

***kocujsitemap_default_exclude_term***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów tagów i własnych taksonomii do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów tagów i własnych taksonomii do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę tagów i własnych taksonomii do wykluczenia dla krótkiego kodu `[KocujSitemap]`.

***kocujsitemap_default_home_link_text***

*Parametry:*

1. tekst (string) - tekst z domyślnym tekstem dla odnośnika do głównej strony
2. tekst (string) - kod aktualnego języka

*Zwracana wartość:*

1. tekst (string) - tekst z domyślnym tekstem dla odnośnika do głównej strony

*Opis:*

Ten filtr ustawia domyślny tekst, który jest używany dla odnośnika do głównej strony w mapie strony. Używany on jest jeżeli nie określono parametru `homelinktext` w krótkim kodzie `[KocujSitemap]` lub gdy nie określono parametru `$homeLinkText` w funkcji PHP `kocujsitemap_show_sitemap`.

***kocujsitemap_default_main_css_class***

*Parametry:*

1. tekst (string) - tekst z wszystkimi domyślnymi klasami CSS dla kontenera mapy strony (elementu blokowego `div` lub `nav`)

*Zwracana wartość:*

1. tekst (string) - tekst z wszystkimi domyślnymi klasami CSS dla kontenera mapy strony (elementu blokowego `div` lub `nav`)

*Opis:*

Ten filtr ustawia domyślną klasę CSS, która jest używana w blokowym elemencie mapy strony. Filtr ten jest używany, gdy nie podano parametru `class` w krótkim kodzie `[KocujSitemap]` lub gdy nie podano parametru `$class` w funkcji PHP `kocujsitemap_show_sitemap`.

***kocujsitemap_default_menus***

*Parametry:*

1. tablica (array) - lista identyfikatorów menu

*Zwracana wartość:*

1. tablica (array) - lista identyfikatorów menu

*Opis:*

Ten filtr ustawia domyślną listę menu, która jest wyświetlana w mapie strony jeżeli lista "opcje listy menu" w panelu administracyjnym jest pusta.

***kocujsitemap_element***

*Parametry:*

1. tekst (string) - tekst dla odnośnika do aktualnego elementu (cały znacznik HTML `<a>`)
2. liczba całkowita (int) - identyfikator elementu lub 0 dla odnośnika do głównej strony
3. tekst (string) - typ elementu; dostępne wartości: "post" dla wpisu, "page" dla strony, "menu" dla elementu menu, "category" dla kategorii wpisów, "author" dla autora, "tag" dla tagu wpisów, "custom" dla wpisu własnego typu, "taxonomy" dla własnej taksonomii i "home" dla odnośnika do głównej strony.
4. tekst (string) - kod aktualnego języka

*Zwracana wartość:*

1. tekst (string) - tekst dla odnośnika do aktualnego elementu (cały znacznik HTML `<a>`)

*Opis:*

Ten filtr ustawia tekst dla odnośnika do aktualnego elementu.

***kocujsitemap_element_home_link_text_position***

*Parametry:*

1. liczba całkowita (int) - pozycja tekstu w odnośniku do głównej strony
2. tekst (string) - tekst dla odnośnika do aktualnego elementu (cały znacznik HTML `<a>`)
3. tekst (string) - kod aktualnego języka

*Zwracana wartość:*

1. liczba całkowita (int) - pozycja tekstu w odnośniku do głównej strony

*Opis:*

Ten filtr ustawia pozycję tekstu w odnośniku do głównej strony. Np. jeżeli odnośnik jest w postaci `<a href="mainpage.html">Main page</a>`, pozycja tekstu odnośnika do głównej strony będzie ustawiona na `14`.

***kocujsitemap_first_element_css_class***

*Parametry:*

1. tekst (string) - tekst z klasą CSS dla pierwszego elementu mapy strony

*Zwracana wartość:*

1. tekst (string) - tekst z klasą CSS dla pierwszego elementu mapy strony

*Opis:*

Ten filtr ustawia klasę CSS, która jest używana w pierwszym elemencie mapy strony.

***kocujsitemap_link_text***

*Parametry:*

1. tekst (string) - tekst z tekstem odnośnika
2. liczba całkowita (int) - identyfikator elementu lub 0 dla odnośnika do głównej strony
3. tekst (string) - typ elementu; dostępne wartości: "post" dla wpisu, "page" dla strony, "menu" dla elementu menu, "category" dla kategorii wpisów, "author" dla autora, "tag" dla tagu wpisów, "custom" dla wpisu własnego typu, "taxonomy" dla własnej taksonomii i "home" dla odnośnika do głównej strony.
4. tekst (string) - kod aktualnego języka

*Zwracana wartość:*

1. tekst (string) - tekst z tekstem odnośnika

*Opis:*

Ten filtr ustawia tekst odnośnika.

***kocujsitemap_widget_default_exclude_author***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów autorów do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów autorów do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę autorów do wykluczenia dla widgetu.

***kocujsitemap_widget_default_exclude_category***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów kategorii wpisów do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów kategorii wpisów do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę kategorii wpisów do wykluczenia dla widgetu.

***kocujsitemap_widget_default_exclude_post***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów wpisów, stron i wpisów własnych typów do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów wpisów, stron i wpisów własnych typów do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę wpisów do wykluczenia dla widgetu.

***kocujsitemap_widget_default_exclude_term***

*Parametry:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów tagów i własnych taksonomii do wykluczenia

*Zwracana wartość:*

1. tekst (string) - oddzielona przecinkami lista identyfikatorów tagów i własnych taksonomii do wykluczenia

*Opis:*

Ten filtr ustawia domyślną listę tagów i własnych taksonomii do wykluczenia dla widgetu.

***shortcode_atts_kocujsitemap***

*Parametry:*

1. tablica (array) - wyjściowa tablica z atrybutami krótkiego kodu
2. tablica (array) - akceptowane parametry i ich wartości domyślne
3. tablica (array) - wejściowa tablica z atrybutami krótkiego kodu

*Zwracana wartość:*

1. tablica (array) - wyjściowa tablica z atrybutami krótkiego kodu

*Opis:*

Ten filtr zmienia atrybuty dla krókiego kodu `[KocujSitemap]`.

== Installation ==

Niniejsza sekcja opisuje sposób instalacji wtyczki i jej uruchomienie.

1. Skopiuj wszystkie pliki do katalogu `/wp-content/plugins/kocuj-sitemap`,
2. Aktywuj wtyczkę używając menu *Wtyczki* w WordPressie,
3. Skonfiguruj opcję *Mapa strony* w panelu administracyjnym,
4. Wykonaj jedną z poniższych czynności: użyj krótkiego kodu `[KocujSitemap]` wewnątrz wpisu dowolnego typu, wyświetl widget lub użyj funkcji PHP gdziekolwiek z kodzie, np. w motywie.

== Screenshots ==

1. Przykład mapy strony.
2. Przykład mapy strony podzielonej na sekcje.
3. Wtyczka posiada opcje służące do wybrania co będzie wyświetlane w mapie strony.
4. Użytkownik może łatwo wybrać kolejność elementów w mapie strony.
5. Użytkownik może łatwo wybrać które menu będą wyświetlane w mapie strony.
6. Użytkownik może łatwo wybrać które własne typy wpisów będą wyświetlane w mapie strony.
7. Niniejsza wtyczka jest zgodna z wtyczką qTranslate X.
8. Istnieje możliwość ustawienia własnej nazwy dla każdej sekcji we wszystkich obsługiwanych językach.
9. Dla każdej strony ustawień istnieje pomoc.

== Frequently Asked Questions ==

Niektóre poniższe pytania pochodzą od użytkowników tej wtyczki, a niektóre od autora tej wtyczki jako uzupełnienie dokumentacji.

= W jaki sposób mogę dodać pytanie dotyczące tej wtyczki? =

Proszę wysłać pytanie do autora pod adresem [http://kocujsitemap.wpplugin.kocuj.pl/contact/](http://kocujsitemap.wpplugin.kocuj.pl/contact/).

= Dlaczego ta wtyczka jest zgodna jedynie z dwiema ostatnimi wersjami WordPressa? =

Zbyt często zdarza się, że używana jest stara wersja WordPressa i nikt nie wykonuje aktualizacji. Brak aktualizacji WordPressa utrudnia twórcom wtyczek (takich jak *Kocuj Sitemap*) zapanowanie nad wsparciem technicznym i zachowaniem zgodności wstecz. Dlatego podjąłem decyzję, że tylko 2 wersje WordPressa będą obsługiwane - najnowsza i poprzednia. Np. jeżeli ostatnią wersją WordPressa jest 4.3, to obsługiwane są wersje 4.2 i 4.3.

Co jednak mają zrobić posiadacze starszych wersji WordPressa? Odpowiedź jest prosta - aktualizować.

Należy pamiętać, że nieaktualna wersja WordPressa to same kłopoty. Prędzej czy później Twoja strona internetowa stanie się celem skutecznego ataku hakera. Dlatego aktualizowanie powinno być procesem wykonywanym jak najczęściej.

Ale dlaczego ludzie nie aktualizują WordPressa? Najczęściej podaje się parę tych samych powodów. Spróbuję poniżej z nimi polemizować.

* *Wtyczka, którą używamy przestanie działać:* Jeżeli autor wtyczki ją porzucił i nie zapewnia już aktualizacji, należy poszukać innej wtyczki, która zapewni tą samą lub podobną funkcjonalność. Ewentualnie można, w celu naprawienia tego problemu, zatrudnić specjalistę, który naprawi wtyczkę, znajdzie inną lub napisze własne rozwiązanie. Czasami okazuje się, że wtyczka, która nie jest oznaczona przez jej autora jako działająca w najnowszej wersji WordPressa, działa prawidłowo. Warto więc to przetestować.
* *Używany motyw lub cała strona przestanie działać:* Jeżeli używany motyw stwarza problemy w najnowszej wersji WordPressa, należy skontaktować się z jego autorem i ustalić warunki dokonania poprawki. Ewentualnie, podobnie jak w przypadku niedziałającej wtyczki, można zatrudnić specjalistę, który naprawi motyw.
* *Moje zmiany w WordPressie przepadną:* Nigdy, przenigdy, nie wolno niczego zmieniać w WordPressie! Przy pierwszej aktualizacji WordPressa, wszystkie Twoje zmiany przepadną! Jeżeli potrzebujesz jakiejś zmiany w WordPressie, powinieneś znaleźć wtyczkę z odpowiednią funkcjonalnością.

Jeżeli mimo powyższych argumentów nadal chcesz pozostać przy starej wersji WordPressa, to nie będziesz mógł używać najnowszych wersji wtyczki *Kocuj Sitemap*. Oczywiście, możesz próbować, ale autor niniejszej wtyczki nie może zagwarantować, że wszystkie funkcje będą działać prawidłowo.

= W jaki sposób utworzyć więcej niż jedną mapę strony? =

Nie ma możliwości utworzenia więcej niż jedna mapa strony na jednej stronie internetowej. Jedyną możliwością do zrobienia tego jest posiadanie wieloblogowej instalacji WordPressa i dodanie mapy strony do każdej strony w sieci indywidualnie.

Pomysł na tą wtyczkę dotyczy tworzenia jednej globalnej mapy strony z odnośnikami do wszystkich wymaganych miejsc. Jednakże, ponieważ niektórzy ludzie potrzebują wiele map strony, ta funkcjonalność jest planowana dla jednej z przyszłych wersji tej wtyczki. Więcej informacji będzie dostępnych, gdy ta funkcjonalność zostanie dodana do planu rozwoju wtyczki.

= W mapie strony wyświetlane są nieprawidłowe tytuły odnośników, a niektóre pozycje nie są pokazywane. Co powinienem zrobić? =

Czasami niektóre wtyczki mogą powodować pewne zmiany w treści używanej przez mapę strony, np. w tytule wpisy. Jest to bardzo rzadka sytuacja, ale jednak możliwa.

Istnieje możliwość zaktualizowania treści oraz pozbycia się tego problemu na przyszłość. Możesz wejść do ustawień administracyjnych dla wtyczki *Kocuj Sitemap* (podmenu *Podstawowe ustawienia*) i kliknąć na przycisku *Zapisz ustawienia*. Podczas zapisywania ustawień odbywa się proces odtworzenia pamięci podręcznej, więc nowa treść będzie używana od tego momentu.

Aby upewnić się, że problem nie będzie więcej występował, możesz wyłączyć pamięć podręczną. Jest to wysoce niezalecane, ponieważ może spowolnić wczytywanie strony internetowej i będzie używało więcej zasobów do pobierania danych z bazy danych. Jednakże jeżeli wiesz, że jedna z Twoich wtyczek zmienia treść, powinieneś wyłączyć pamięć podręczną, aby nie wyświetlać nieprawidłowych danych w Twojej mapie strony. Aby tego dokonać należy wejść do ustawień administracyjnych dla wtyczki *Kocuj Sitemap* (podmenu *Podstawowe ustawienia*), kliknąć na zakładce *Rozwiązywanie problemów*, odznaczyć opcję *Aktywuj pamięć podręczną* i kliknąć na przycisku *Zapisz ustawienia*. Od teraz wtyczka *Kocuj Sitemap* nie będzie używała pamięci podręcznej.

= Moja wielopoziomowa mapa strony nie ma wcięć. Co powinienem zrobić? =

Czasami wielopoziomowa mapa wyświetlana jest bez dodatkowych marginesów z lewej strony. Nie jest to wina tej wtyczki, ale jest to problem w Twoim motywie! Niniejsza wtyczka nie tworzy wyglądu mapy strony. Jest to zadanie dla Twojego motywu. Kiedy ten problem występuje, nie dotyczy on jedynie mapy strony, ale wszystkich wielopoziomowych list na Twojej stronie internetowej. Powinieneś skontaktować się z autorem Twojego motywu i on powinien poprawić ten błąd.

Jeżeli nie możesz naprawić tego problemu z jakiegokolwiek powodu (autor motywu jest niedostępny, nie może naprawić błędu, itp.), wtyczka *Kocuj Sitemap* ma rozwiązanie. Jednak to rozwiązanie jest przeznaczone tylko dla mapy strony - problemy z innymi wielopoziomowymi listami nie będą w żaden sposób poprawione. Aby naprawić problem z wielopoziomową mapą strony, należy wejść do ustawień administracyjnych dla wtyczki *Kocuj Sitemap* (podmenu *Podstawowe ustawienia*), kliknąć na zakładce *Rozwiązywanie problemów*, wpisać wymagany lewy margines do pola *Wymuś lewy margines w pikselach dla każdego poziomu w wielopoziomowej liście* i kliknąć na przycisku *Zapisz ustawienia*.

= Jak mogę usunąć duplikaty z mapy strony? =

W przypadku menu istnieje możliwość posiadania duplikatów, ponieważ różne menu mogą mieć te same elementy jak inne. Aby usunąć te duplikaty z mapy strony należy wejść do ustawień menu dla wtyczki *Kocuj Sitemap* (podmenu *Opcje listy menu*), kliknąć na zakładce *Opcje*, zaznaczyć opcję *Usuwaj duplikaty elementów menu z mapy strony* i kliknąć na przycisku *Zapisz opcje listy menu*.

= Mapa strony wygląda dziwnie. Co powinienem zrobić? =

Wygląd mapy strony nie jest związany z funkcjonalnością wtyczki *Kocuj Sitemap*, ale z motywem używanym na Twojej stronie internetowej. Mapa strony używa prawidłowych znaczników HTML, więc w każdym motywie będzie wyglądała tak, jak powinna.

Istnieje jedna możliwość, która może powodować problemy z wyglądem mapy strony. Powinieneś sprawdzić, czy Twój motyw jest utworzony w HTML 5. Jeżeli nie, należy wejść do ustawień administracyjnych dla wtyczki *Kocuj Sitemap* (podmenu *Podstawowe ustawienia*), kliknąć na zakładce *Format mapy strony*, odznaczyć opcję *Używaj znaczników HTML 5* i kliknąć na przycisku *Zapisz ustawienia*.

= Posiadam wtyczkę do obsługi wielu języków, ale moja mapa strony jest tylko w jednym języku - domyślnym dla mojej instalacji WordPressa. Dlaczego? =

W pierwszej kolejności musisz sprawdzić, czy Twoja wtyczka do obsługi wielu języków jest wspierana przez wtyczkę *Kocuj Sitemap*. Obecnie istnieje jedna wtyczka do obsługi wielu języków, która jest używana: qTranslate X. Pamiętaj, że każde odgałęzienie tych wtyczek może również nie działać, ponieważ mogły tam zostać wprowadzone pewne zmiany w nazwach klas lub funkcji. Wtyczka *Kocuj Sitemap* gwarantuje pełną współpracę tylko z tą jedną wtyczką na chwilę obecną - każda inna wtyczka może być ignorowana lub działać jedynie częściowo.

Jednak jeśli używasz wspieranej wtyczki i masz pewne problemy, powinieneś sprawdzić, czy nie posiadasz więcej zainstalowanych i aktywowanych wtyczek do obsługi wielu języków. Zawsze jest złym pomysłem używanie więcej niż jednej wtyczki do obsługi wielu języków. Jednak jeżeli masz jakikolwiek powód to używania wielu tych wtyczek, powinieneś dokonać pewnych sprawdzeń w ustawieniach wtyczki *Kocuj Sitemap*. Aby to zrobić należy wejść do ustawień administracyjnych dla wtyczki *Kocuj Sitemap* (podmenu *Podstawowe ustawienia*), kliknąć na zakładce *Wtyczki do obsługi wielu języków* i sprawdzić jaka wtyczka do obsługi wielu języków jest wyświetlona jako *Aktualnie używana wtyczka*. Jeżeli chcesz ją zmienić, powinieneś wybrać wymaganą wtyczkę do obsługi wielu języków w opcji *Używaj wtyczki dla wielu języków* i kliknąć na przycisku *Zapisz ustawienia*.

Istnieje również możliwość, że jakaś inna wtyczka zakłóca Twoje treści. Istnieje możliwość zaktualizowania treści oraz pozbycia się tego problemu na przyszłość. Możesz wejść do ustawień administracyjnych dla wtyczki *Kocuj Sitemap* (podmenu *Podstawowe ustawienia*) i kliknąć na przycisku *Zapisz ustawienia*. Podczas zapisywania ustawień odbywa się proces odtworzenia pamięci podręcznej, więc nowa treść będzie używana od tego momentu.

Aby upewnić się, że problem nie będzie więcej występował, możesz wyłączyć pamięć podręczną. Jes to wysoce niezalecane, ponieważ może spowolnić wczytywanie strony internetowej i będzie używało więcej zasobów do pobierania danych z bazy danych. Jednakże jeżeli wiesz, że jedna z Twoich wtyczek zmienia treść, powinieneś wyłączyć pamięć podręczną, aby nie wyświetlać nieprawidłowych danych w Twojej mapie strony. Aby tego dokonać należy wejść do ustawień administracyjnych dla wtyczki *Kocuj Sitemap* (podmenu *Podstawowe ustawienia*), kliknąć na zakładce *Rozwiązywanie problemów*, odznaczyć opcję *Aktywuj pamięć podręczną* i kliknąć na przycisku *Zapisz ustawienia*. Od teraz wtyczka *Kocuj Sitemap* nie będzie używała pamięci podręcznej.

== Changelog ==

= 2.7.0 =
* 0000383: [Inne] Brak deklaracji używanych przestrzeni nazw.
* 0000384: [Inne] Przeformatowanie kodu do PSR-2.
* 0000385: [Błąd] Problemy bezpieczeństwa.

= 2.6.4 =
* 0000382: [Błąd] Pewne błędy podczas zapisywania opcji.

= 2.6.3 =
* 0000381: [Błąd] Czasami występuje wyjątek dotyczący nie istniejącego okna, gdy WordPress jest wyświetlany w utworzonym oknie, takim jak dodającym media.

= 2.6.2 =
* 0000379: [Błąd] Wpisy własnych typów nie są dostępne w panelu administracyjny, gdy zostały utworzone przez jakąś wtyczkę (np. CPT UI).

= 2.6.1 =
Dodano brakujące pliki JavaScript.

= 2.6.0 =
* 0000378: [Funkcjonalność] Dodać opcję widgetu z wykluczaniem całych typów elementów z wyświetlania.
* 0000375: [Funkcjonalność] Dodać atrybut krótkiego kodu dla wykluczania całych typów elementów z wyświetlania.
* 0000366: [Funkcjonalność] Dodać opcję do wyświetlania mapy strony jako rozwijanego menu w widgecie.
* 0000371: [Funkcjonalność] Usunąć sprawdzanie czy tablica jest pusta przed "foreach".
* 0000365: [Błąd] Zabronić podawania wartości zmiennoprzecinkowej, gdy wartość całkowita jest wymagana w ustawieniach wtyczki.

= 2.5.1 =
* 0000364: [Błąd] Usunięcie niepotrzebnych akcji i filtrów z "personalizacji", ponieważ mogą one spowodować, iż ta opcja będzie działała nieprawidłowo w niektórych przypadkach.

= 2.5.0 =
* 0000350: [Funkcjonalność] Zmiana niektórych tekstów dla opcji i dokumentacji.
* 0000363: [Funkcjonalność] Nie należy pozwalać na wysyłanie informacji o stronie internetowej do autora wtyczki, gdy adres jest lokalny.
* 0000362: [Błąd] Pamięć podręczna nie jest usuwana ze strony, która została usunięta z sieci Multisite.
* 0000358: [Funkcjonalność] Dodać przyciski "wsparcie" i "pomóż w tłumaczeniu" do stron ustawień.
* 0000353: [Funkcjonalność] Dodać opcję do usuwania duplikatów, gdy wyświetlane są różne menu.
* 0000361: [Błąd] Wyłączenie taksonomii dla własnych typów wpisów nie działa.
* 0000359: [Funkcjonalność] Dodać opcję do dodania tekstu "utworzone przez" w widgecie z mapą strony.
* 0000349: [Funkcjonalność] Zreorganizowanie ustawienia w panelu administracyjnym.
* 0000352: [Funkcjonalność] Zoptymalizowanie użycie pamięci.
* 0000356: [Błąd] Okno modalne w panelu administracyjnym nie jest wyświetlane prawidłowo na urządzeniach mobilnych.
* 0000355: [Błąd] Panele meta w ustawieniach nie są wyświetlane prawidłowo na urządzeniach mobilnych.
* 0000341: [Funkcjonalność] Usunąć stare metody (bez żadnego i z interfejsem MultipleLanguages) służące do dodawania własnego wsparcia dla wielojęzycznych wtyczek.
* 0000351: [Funkcjonalność] Dodać możliwość do wyłączenia wielojęzyczności dla wtyczki.
* 0000345: [Funkcjonalność] Pozwolić na ustawienie czy deinstalacja wtyczki usunie swoje ustawienia z bazy danych czy nie.
* 0000348: [Błąd] W trybie Multisite dane starych wersji aktualizują się tylko dla głównej strony.
* 0000286: [Funkcjonalność] Reorganizacja kodu, który obsługuje ustawienia wtyczki.
* 0000347: [Funkcjonalność] Zmiana jednorazowego widgetu na wielorazowy.
* 0000282: [Funkcjonalność] Usunięcie użycia "jQuery UI" dla zakładek w panelu administracyjnym.
* 0000344: [Funkcjonalność] Usunięcie niepotrzebnego sprawdzania wartości ustawień w plikach JavaScript.
* 0000340: [Funkcjonalność] Usunięcie starych filtrów z wersji 1.x.x.
* 0000339: [Błąd] Użycie stałych PHP w kontekście, który może być nieprawidłowy.

= 2.4.0 =
* 0000354: [Funkcjonalność] Dodanie wsparcia dla WordPress 4.7.

= 2.3.5 =
* 0000343: [Błąd] Czasami usunięcie tej wtyczki z panelu administracyjnego nie jest możliwe.

= 2.3.4 =
* 0000342: [Błąd] Krytyczny błąd PHP przy aktualizacji w PHP 5.3.

= 2.3.3 =
* 0000338: [Błąd] Błąd na stronie widocznej dla użytkowników dla stron nie posiadających wielu języków.

= 2.3.2 =
* 0000337: [Błąd] Aktualizacja ze starszych wersji może się nie udać.
* 0000336: [Błąd] Atrybuty dodawane bez odstępu pomiędzy tagiem a atrybutem w HTML 5.

= 2.3.1 =
* 0000335: [Błąd] Panel administracyjny jest zablokowany z powodu błędu.

= 2.3.0 =
* 0000329: [Funkcjonalność] Optymalizacja rozmiaru wtyczki.
* 0000334: [Błąd] Nieprawidłowy odnośnik do oceny wtyczki.
* 0000333: [Funkcjonalność] Nie wymuszać wyświetlania informacji o aktualizacji, ale wyświetlać odnośnik do niej w górnej wiadomości.
* 0000332: [Funkcjonalność] Dodać stare metody własnego wsparcia dla wielu języków na listę przestarzałych metod.
* 0000331: [Funkcjonalność] Usunąć klasy CSS "tag*" z elementów HTML.
* 0000318: [Funkcjonalność] Usunąć wsparcie dla wtyczki qTranslate.
* 0000330: [Błąd] Własne klasy PHP dla wielojęzycznego wsparcia nie działają.
* 0000327: [Błąd] Nieprawidłowe ustawienia uprawnień, gdy tworzony jest katalog.
* 0000328: [Błąd] Zmienić separator katalagów "/" na stałą DIRECTORY_SEPARATOR dla prawidłowego działania w systemie Windows.

= 2.2.5 =
* 0000325: [Błąd] Podczas tworzenia mapy strony na stronie widocznej dla odwiedzających (nie w panelu administracyjnym), niektóre teksty nie są tłumaczone.
* 0000326: [Błąd] Wtyczka qTranslate wykrywana jest wtedy, gdy qTranslate X jest zainstalowane i aktywowane w trybie zgodności.

= 2.2.4 =
* 0000320: [Błąd] Odnośniki do licencji, odnośnik do wysyłania podziękowań do autora i informacja o aktualizacji w panelu administracyjnym nie działają w trybie "multisite".

= 2.2.3 =
* 0000319: [Błąd] Podczas używana opcji dla własnych typów wpisów występuje błąd w PHP.

= 2.2.2 =
* 0000317: [Błąd] Czasami kategorie lub inne typy taksonomii nie są tłumaczone na inny język.

= 2.2.1 =
* 0000316: [Błąd] Aktualizacja nie zapisuje wymaganych danych do bazy danych.

= 2.2.0 =
* 0000315: [Funkcjonalność] Zotymalizować zapytania do bazy danych w panelu administracyjnym.
* 0000314: [Funkcjonalność] Reorganizać i zoptymalizować kodu.
* 0000312: [Funkcjonalność] Dodać informację o usunięciu wsparcia dla qTranslate.
* 0000313: [Błąd] Czasami kategorie wpisów nie są sortowane poprawnie.

= 2.1.0 =
* 0000301: [Funkcjonalność] Dodać zgodność wtyczki z PHP 7.
* 0000311: [Błąd] Niemożliwe jest wykluczenie czegokolwiek w widgecie mapy strony.
* 0000303: [Funkcjonalność] Dodać lepszą obsługę błędów.
* 0000310: [Błąd] Brak przycisku krótkiego kodu w edytorze wizualnym.
* 0000309: [Błąd] Katalog pamięci podręcznej nie zawsze jest automatycznie tworzony.
* 0000290: [Funkcjonalność] Zoptymalizować skrypty JS.
* 0000299: [Funkcjonalność] Dodać opcję do ustawiania lewego marginesu w mapie strony.
* 0000307: [Funkcjonalność] Użyć funkcji PHP 7 "random_int" zamiast "rand", gdy wymagana jest lepsza wartość losowa.
* 0000304: [Błąd] Dodać formatowanie do atrybutów HTML.
* 0000302: [Funkcjonalność] Dodać dokumenację JsDoc do wszystkich plików JavaScript.
* 0000300: [Funkcjonalność] Zminimalizować skrypty JavaScript w panelu administracyjnym.
* 0000288: [Funkcjonalność] Usunąć niepotrzebne fragmenty przestrzeni nazw.
* 0000287: [Funkcjonalność] Zreorganizować kod dla przycisków Quicktags i TinyMCE.
* 0000285: [Funkcjonalność] Nie wczytywać niepotrzebnych skryptów JS w panelu administracyjnym.
* 0000308: [Funkcjonalność] Usunąć niepotrzebne "eval" z wczytywania pliku zabezpieczającego pamięć podręczną.

= 2.0.6 =
* 0000289: [Błąd] Niektóre wtyczki nie mogą zostać zapisane w panelu administracyjnym, gdy wtyczka "Kocuj Sitemap" jest aktywna.

= 2.0.5 =
* 0000284: [Błąd] Dodać wsparcie dla wszystkich typów WordPressa do wsparcia dla qTranslate.

= 2.0.4 =
* 0000283: [Błąd] Naprawić wsparcie dla wtyczki qTranslate.

= 2.0.3 =
* 0000281: [Błąd] Nieprawidłowe wyświetlanie wielu menu.

= 2.0.2 =
Poprawka numerowania wersji dla repozytorium WordPressa (2.0.1 nie mogło zostać zaktualizowane).

= 2.0.1 =
* 0000280: [Błąd] Nieprawidłowe wyświetlanie wybranych menu w panelu administracyjnym.

= 2.0.0 =
* 0000279: [Błąd] Poprawić wysyłanie "podziękowań", gdy panel administracyjny używa protokołu "https".
* 0000154: [Funkcjonalność] Dodać możliwość wyłączenia pamięci podręcznej; czasami jest to konieczne do współpracy z innymi wtyczkami.
* 0000204: [Błąd] Naprawić problemy z wpisami w hierarchicznych kategoriach.
* 0000206: [Funkcjonalność] Usunąć zgodność z Wordpressami w wersjach starszych niż 4.3.
* 0000270: [Błąd] Naprawić nieprawidłowe sortowanie wg nazw i tytułów, gdy wielojęzyczna wtyczka jest w użyciu.
* 0000145: [Funkcjonalność] Dodać więcej informacji pomocniczych.
* 0000276: [Funkcjonalność] Dodać informację do trybu "debug" o akcjach i filtrach wtyczki, które nie są już więcej używane.
* 0000277: [Funkcjonalność] Zmienić nazwy filtrów dla lepszej zgodności z oryginalnymi nazwami filtrów w WordPressie.
* 0000271: [Funkcjonalność] Zoptymalizować rozmiar pliku pamięci podręcznej.
* 0000266: [Funkcjonalność] Dodać używanie qTranslate-X dla wielojęzycznej zawartości.
* 0000267: [Funkcjonalność] Ustawić tłumaczenia tak, aby były zgodne z wymaganiami dla translate.wordpress.org.
* 0000223: [Funkcjonalność] Zmienić wszystkie porównania na dokładne porównania ("strict comparisions") w PHP i JavaScript.
* 0000264: [Funkcjonalność] Zablokować wysyłanie podziękowań, gdy adres IP jest lokalny.
* 0000207: [Błąd] Gdy wtyczka "qTranslate" jest zainstalowana lub skonfigurowana po wtyczce "Kocuj Sitemap", pamięć podręczna może mieć nieprzetłumaczone teksty.
* 0000216: [Funkcjonalność] Przenieść katalog pamięci podręcznej do katalogu "wp-content/cache/kocuj-sitemap".
* 0000215: [Funkcjonalność] Usunąć wszystkie funkcje extract() z kodu.
* 0000213: [Funkcjonalność] Usunąć tagi końcowe PHP.
* 0000205: [Funkcjonalność] Zmienić tryb zgodności HTML 5 z IE na komentarze warunkowe zamiast sprawadzania przeglądarki internetowej w PHP.
* 0000169: [Funkcjonalność] Dodać zgodnośc z wieloma blogami.
* 0000202: [Funkcjonalność] Dodać więcej filtrów.
* 0000178: [Funkcjonalność] Dodać okienko w panelu administracyjnym po aktualizacji z wersji 1.x.x z informacją o ważnych zmianach w wersji 2.0.0.
* 0000179: [Funkcjonalność] Dodać możliwość zmiany nazwy sekcji dla każdego języka.
* 0000181: [Funkcjonalność] Usunąć niepotrzebne "eval()" z kodu.
* 0000180: [Funkcjonalność] Usunąć zadania uruchamiane co pewien czas ("cron jobs") z wtyczki.
* 0000168: [Funkcjonalność] Dodać więcej opcji dla każdego wyświetlanego typu w mapie strony.
* 0000142: [Funkcjonalność] Dodać widget z mapą strony.
* 0000160: [Funkcjonalność] Dodać możliwość wykluczania innych pozycji niż wpisy i strony, np. kategorii.
* 0000150: [Błąd] Zmiany w ustawieniach bezpośrednich odnośników powinny odtworzyć pamięć podręczną.
* 0000162: [Funkcjonalność] Zabezpieczyć katalog pamięci podręcznej.
* 0000157: [Funkcjonalność] Zreorganizować menu wtyczki w panelu administracyjnym.
* 0000153: [Funkcjonalność] Zreorganizować cały kod.
* 0000158: [Funkcjonalność] Dodać informację o możliwości oceny tej wtyczki.
* 0000148: [Funkcjonalność] Dodać wsparcie dla własnych typów wpisów.
* 0000159: [Funkcjonalność] Dodać możliwość wyświetlania listy autorów i listy tagów.
* 0000149: [Funkcjonalność] Dodać możliwość podziału mapy strony na sekcje (wpisy, strony, itp.).
* 0000146: [Funkcjonalność] Pozwolić administratorowi na wyłączenie przycisku mapy strony w edytorze HTML i wizualnym.
* 0000156: [Funkcjonalność] Tworzyć pamięć podręczną na tzw. "frontend" czyli stronie internetowej (dla zgodności z niektórymi wtyczkami, głównie dla wykluczenia wpisów) i dodanie opcji do wyłączenia tego.
* 0000143: [Funkcjonalność] Dodać opcję do wykluczenia niektórych wpisów lub stron z mapy strony.
* 0000155: [Funkcjonalność] Dodać opcję "ustawienia" do menu wtyczek w panelu administracyjnym.

= 1.3.3 =
* 0000167: [Błąd] Poprawić tworzenie pamięci podręcznej mapy strony zbyt wiele razy po zmianie jakiejkolwiek opcji.

= 1.3.2 =
Nie ma żadnych nowych funkcjonalności lub poprawek - tylko informacja czy Twój serwer lub konto hostingowe jest gotowe na wersję 2.0.0 tej wtyczki.

= 1.3.1 =
* 0000268: [Błąd] Naprawić brak zgodności ze standardową klasą menu w WordPressie.
* 0000269: [Funkcjonalność] Dodać "index.html" do wszystkich katalogów dla zwiększenia ochrony.

= 1.3.0 =
* 0000161: [Funkcjonalność] Dodać język francuski.

= 1.2.0 =
* 0000139: [Błąd] Naprawić wyzwalacz rejestracyjny wtyczki.
* 0000140: [Błąd] Poprawić bezpieczeństwo formularzy w panelu administracyjnym.
* 0000127: [Funkcjonalność] Dodać wsparcie dla wtyczki qTranslate, aby prawidłowo wyświetlać odnośniki i tytuły.
* 0000129: [Funkcjonalność] Dodać dodatkowe informacje o autorze do panelu administracyjnego (takie jak odnośnik do Facebooka).
* 0000130: [Funkcjonalność] Dodać przycisk w edytorze wizualnym i HTML do automatycznego dodawania krótkiego kodu wtyczki.
* 0000131: [Funkcjonalność] Dodać więcej tekstu dla użytkowników do pomocy i dokumentację.
* 0000132: [Funkcjonalność] Dodać dodatkowe informacje pomocnicze dla każdej sekcji w menu administracyjnym dla tej wtyczki.
* 0000133: [Funkcjonalność] Dodać możliwość zmiany listy menu w ustawieniach wtyczki bez przeładowywania całej strony.
* 0000134: [Funkcjonalność] Zmienić możliwość wysyłania podziękowań do autora, aby były możliwe po 1 dniu używania wtyczki (nie zaraz po zainstalowaniu jej).
* 0000135: [Funkcjonalność] Zmienić wczytywanie klas PHP do panelu administracyjnego, aby działo się to tylko podczas jego działania, aby przyspieszyć wykonywanie skryptów.
* 0000136: [Funkcjonalność] Dodać możliwość zmiany kolejności listy menu w ustawieniach administracyjnych dla tej wtyczki.

= 1.1.1 =
* 0000137: [Błąd] Naprawić zgodność z PHP 5.4.

= 1.1.0 =
* 0000123: [Błąd] Sprawdzić i poprawić wszystkie problemy z plikiem readme.txt, aby poprawić wyświetlanie informacji w repozytorium wordpress.org.
* 0000122: [Funkcjonalność] Dodać pomoc dla wtyczki do panelu administracyjnego.

= 1.0.0 =
* Pierwsza wersja wtyczki.

== Upgrade Notice ==

= 2.6.4 =
Poprawiono błąd w panelu administracyjnym.

= 2.6.3 =
Poprawiono błąd dla okna dodawania mediów.

= 2.6.2 =
Poprawiono błąd dla wpisów własnych typów.

= 2.6.1 =
Dodano brakujące pliki JavaScript.

= 2.6.0 =
Dodano możliwość wykluczania całych typów elementów (np. wszystkich wpisów), dodano możliwość wyświetlania mapy strony jako rozwijanej listy w widgecie.

= 2.5.1 =
Poprawiono rzadki błąd w "personalizacji".

= 2.5.0 =
Dodano usuwanie duplikatów. Parę optymalizacji. Poprawiono parę błędów.

= 2.4.0 =
Dodano wsparcie dla WordPress 4.7.

= 2.3.5 =
Poprawiono jeden błąd.

= 2.3.4 =
Poprawiono jeden błąd.

= 2.3.3 =
Poprawiono jeden błąd.

= 2.3.2 =
Poprawiono parę błędów.

= 2.3.1 =
Naprawiono krytyczny błąd.

= 2.3.0 =
Usunięto wsparcie dla wtyczki qTranslate, zoptymalizowano część kodu i poprawiono parę błędów.

= 2.2.5 =
Poprawiono jeden błąd.

= 2.2.4 =
Poprawiono jeden błąd w panelu administracyjnym dla trybu "multisite".

= 2.2.3 =
Poprawiono jeden błąd.

= 2.2.2 =
Poprawiono jeden błąd.

= 2.2.1 =
Poprawiono jeden błąd.

= 2.2.0 =
Zoptymalizowano i zreorganizowano kod. Poprawiono parę błędów.

= 2.1.0 =
Dodano zgodność z PHP 7 i możliwość wymuszania lewych marginesów w wielopoziomowej mapie strony. Poprawiono parę błędów.

= 2.0.6 =
Poprawiono błąd dotyczący zapisywania ustawień dla innych wtyczek.

= 2.0.5 =
Poprawiono błąd dotyczący braku wsparcia dla wszystkich typów wpisów w WordPressie dla qTranslate.

= 2.0.4 =
Poprawiono błąd dotyczący wsparcia dla qTranslate.

= 2.0.3 =
Poprawiono błąd dotyczący wyświetlania wielu menu.

= 2.0.2 =
Brak nowych funkcjonalności lub poprawek błędów - jedynie poprawiono numerowanie wersji w repozytorium WordPressa.

= 2.0.1 =
Poprawiono błąd w panelu administracyjnym.

= 2.0.0 =
UWAGA: TA WERSJA WYMAGA WORDPRESSA MIN. W WERSJI 4.3 I PHP MIN. W WERSJI 5.3! ZMIENIONO FILTRY! Dodano: autorów, tagi i wpisy własnych typów; wykluczanie elementów; dzielenie mapy strony na sekcje; wsparcie dla qTranslate X; pomoc kontekstową; większą kontrolę. Poprawiono drobne błędy.

= 1.3.1 =
Poprawiono drobne błędy.

= 1.3.0 =
Dodano język francuski. Zmieniono niektóre teksty o wtyczce.

= 1.2.0 =
Dodano wsparcie dla stron wielojęzycznych używając wtyczki qTranslate. Zmieniono opcję "lista menu" w panelu administracyjnym, aby umożliwić zmienianie wartości i ich kolejności bez potrzeby przeładowywania strony. Poprawiono niektóre drobne błędy.

= 1.1.1 =
Poprawiono problemy ze zgodnością z PHP 5.4.

= 1.1.0 =
Dodano pomoc do panelu administracyjnego.
