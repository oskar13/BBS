##BBS
### IF 13 veebiprogrameerimis kursuse lõputöö


####Valmis lehestiku kirjeldus
Lehestik koosneb teadetetahvlitest mida admin saab luua ja kustutada, lisada kirjeldusi ning määrata neile moderaatoreid.
Admin ja mod saavad kasutajaid IP või kasutajanime järgi bännida juhul kui kasutaja on rikkunud reegleid.


#### Tähtsamad failid ja nende kirjeldused:
* **index.php** - avaleht kus on nimekiri lehtedest ja viimased postitused
* **board.php** - sisaldab endas koodi mis laeb postitused ja teadetetahvli kirjelduse.
* **thread.php** - kuvab üksikut postitust koos kommentaaridega.
* **upload.php** - piltide üles laadimine ja pisipildi genereerimine.
* **admin.php** - administreermiseks vajalikud funktsioonid.
* **admin_post.php** - Võtab vastu kõik sisestus päringud admin.php lehelt
* **news.php** - kuvab üldiseid uudiseid mis on seotud lehega. Kasutajatel on võimalus kommenteerida uudiseid.
* **normalize.css** - nullib ära võimalikud lehitsejate vahelised erinevused
* **style.css** - üldised CSS stiilid mida jagab enamus lehti
* **board.css** - CSS fail mis määrab stiili tahvlitele


#### Põhimõtted:
* Kõik külastajad on kasutajad, juhul kui kasutaja ei ole sisse loginud on ta anonüümne kasutaja kes peaks omama küpsist mõndade andmetega mis lihtsustavad lehitsemist.
* Kasutajad peavad saama oma postitusi kustutada. Parool seatakse enne postituse saatmist või kasutatakse küpsises hoiustatud parooli.

#### TODO:
- [x] README
- [x] algne andmebaasi skeem
- [ ] leida viis kuidas mugavalt kuvada postitusi mis on omavahel seotud.
- [x] piltide üles laadimise skript 

#### Teadaolevad vead
- Sesiooni kaaperdamine teiselt saidilt mis asub samas serveris


#### Ettepanekud
- tahvlid võiksid olla kategoriseeritud, võimak koostada selle järgi navigatsioon esilehele
- kasutusel erilised koodid millega viidatakse postitusele mida vastatakse, näide ">>123456". Käitub lingina mis viib sind post.php lehele kus vastav kommentaar/postitus on teise värviga määratud.
- url-id tuleb saada normaalsemaks, sammuti serverile saata andmed elegantsemalt ja hoida aadressi ribad puhtamad (.htaccess)
