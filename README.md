##BBS
### IF 13 veebiprogrameerimis kursuse lõputöö


####Valmis lehestiku kirjeldus
Lehestik koosneb teadetetahvlitest mida admin saab luua ja kustutada, lisada kirjeldusi ning määrata neile moderaatoreid.
Admin ja mod saavad kasutajaid IP või kasutajanime järgi bännida juhul kui kasutaja on rikkunud reegleid.


#### Tähtsamad failid ja nende kirjeldused:
* **board.php** - sisaldab endas koodi mis laeb postitused ja teadetetahvli kirjelduse.
* **admin.php** - administreermiseks vajalikud funktsioonid, sellele failile suunatakse kõik admin päringud mis on seotud teadetetahvlitega.
* **news.php** - kuvab üldiseid uudiseid mis on seotud lehega. Kasutajatel on võimalus kommenteerida uudiseid.
* **news-admin.php** - uudiste sisestamine ja uuendamine.
* **normalize.css** - nullib ära võimalikud lehitsejate vahelised erinevused
* **style.css** - üldised CSS stiilid mida jagab enamus lehti
* **board.css** - CSS fail mis määrab stiili tahvlitele


#### Põhimõtted:
* Kõik külastajad on kasutajad, juhul kui kasutaja ei ole sisse loginud on ta anonüümne kasutaja kes peaks omama küpsist mõndade andmetega mis lihtsustavad lehitsemist.
* Kasutajad peavad saama oma postitusi kustutada. Parool seatakse enne postituse saatmist või kasutatakse küpsises hoiustatud parooli.

#### TODO:
[x] README
[x] algne andmebaasi skeem
[ ] leida viis kuidas mugavalt kuvada postitusi mis on omavahel seotud.

#### Teadaolevad vead
- Sesiooni kaaperdamine teiselt saidilt mis asub samas serveris
- Kautaja sisestatud andmete puhastamine puudub


#### Ettepanekud
- HTML ja CSS loomisel võiks kasutada Twitter Bootstrapi
