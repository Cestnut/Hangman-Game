# 1 Idea yo

Il progetto prevede la creazione di una versione online del gioco dell'impiccato. I giocatori avranno a disposizione un certo numero di tentativi per indovinare una parola scelta dal server, tra quelle presenti in una lista predefinita. Dopo ogni tentativo, il giocatore verrà informato su quanto la propria risposta si avvicini alla soluzione esatta.

## 1.1 Modalità di gioco
I giocatori condividono Vite e Indizi, a turno provano a indovinare la parola. Ogni turno ha una durata limitata di tempo.

## 1.2 Stanze
### 1.2.1 Gestione
Gli utenti hanno la possibilità di creare una nuova stanza o unirsi a una esistente dal menù principale. 
Durante la creazione di una stanza, è richiesto di inserire un nome per identificarla (suggerito "nomeutente's room").

Il creatore della stanza, prima di iniziare una partita, deve impostare il tempo massimo per il turno di ogni giocatore e il numero massimo di vite. Una volta impostato, premere il pulsante di avvio per iniziare la partita.

In caso il creatore della stanza decida di uscire, la stanza verrà chiusa. È importante notare che gli utenti non possono unirsi alla stanza mentre una partita è in corso.

### 1.2.2 Chat
All'interno di ogni stanza di gioco, è disponibile una chat testuale che i giocatori potranno utilizzare per comunicare durante la partita, ogni messaggio è accompagnato dal nome del mittente.

# 2 Risorse
Saranno implementate REST API per interagire con le seguenti risorse:
- **User**
- **Room**
- **Wordlist**

# 3 Gioco
Durante la partita, viene stabilito un ordine tra i giocatori. Ogni turno, ciascun giocatore cercherà di indovinare la parola segreta. In caso di errore verrà persa una vita per tutti i giocatori. Se il tempo per il turno scade, si passerà semplicemente al giocatore successivo.


# 4 Database
Il gioco utilizza un database SQL per la memorizzazione dei dati. Verranno presentati in seguito il diagramma ER (Entità-relazione) e lo schema relazionale.
## 4.1 Diagramma ER

![ER_Diagram](ER_Diagram.png)

## 4.2 Schema Relazionale
## 4.2.1 Word
- ID_word (chiave primaria)
- word (30 caratteri)

## 4.2.2 Role
- name (chiave primaria)

## 4.2.3 User	
- ID_user (chiave primaria)
- name (unico, 15 caratteri)
- password
- role (chiave esterna in Role)

## 4.2.4 Room
- ID_room (chiave primaria)
- name (32 caratteri)
- ID_host (chiave esterna in User)

## 4.2.5 Game
- ID_game (chiave primaria)
- max_time (maggiore di 0)
- max_lives (maggiore di 0)
- ID_room (chiave esterna in room)
- ID_word (chiave esterna in word)
- finishTimestamp (default NULL)
- turnPlayerID (chiave esterna in user)
- wordMask (INT)

## 4.2.6 Message
- ID_message (chiave primaria)
- message (128 caratteri)
- timestamp
- ID_user (chiave esterna in user)
- ID_room (chiave esterna in room)

## 4.2.7 Game_Participation
- ID_game_participation (chiave primaria)
- ID_game (chiave esterna in game)
- ID_user (chiave esterna in user)

## 4.2.8 Room_Partecipation
- ID_room (chiave esterna in room)
- ID_user (chiave esterna in user)
- timestamp

ID_room e ID_user compongono la chiave primaria

## 4.2.9 Guess:
- ID_Guess (chiave primaria)
- word (30 caratteri)
- timestamp
- ID_game_participation (chiave esterna in game_participation)

# 5 Applicazione
Per la realizzazione dell'applicazione sono stati usati HTML, CSS, PHP, Javascript (utilizzando anche la libreria jQuery in particolar modo per le chiamate Ajax).

Client e server comunicano tramite chiamate Ajax. In alcuni casi è necessaria la presenza di uno stream di comunicazione da server a client, realizzato tramite Server Sent Events (SSE).
## 5.1 Javascript
Javascript è il linguaggio di scripting client-side utilizzato per rendere dinamica la pagina e per inviare le richieste al server tramite AJAX.

Di seguito verranno presentate i metodi e le tecniche usate più frequentemente.

### 5.1.1 window.onload
Quando una pagina viene caricata, si attiva l'evento "window.onload". Assegnando una funzione a questo evento, essa verrà eseguita una volta che la pagina ha finito di caricare. Ad Esempio:

```js
window.onload = init;

function init(){
    $("#back").on("click", leave);
}

function leave(){
    window.location = "../html/userHome.html";
}
```
La funzione "init" viene utilizzata per assegnare un listener per l'evento click all'elemento con id "back", questo perché garantisce che l'elemento esiste nella pagina prima di assegnare l'evento. In questo modo si evitano possibili comportamenti imprevisti, poiché la funzione viene chiamata solo una volta che la pagina è stata completamente caricata.

### 5.1.2 Manipolazione DOM
Il DOM (Document Object Model), è il modello con cui viene rappresentato il documento. Esso ha una forma ad albero, e tramite JavaScript è possibile manipolarne i nodi aggiungendo, modificando o eliminando elementi a runtime.

Ciò viene fatto ad esempio per popolare la pagina roomList con tutte le stanze aperte.
"message.payload" è una lista di oggetti JSON nella forma {"ID_room":ID, "name":name}.
```js
message.payload.forEach(function (room) {
  var container = document.getElementById("roomList");
                    
  var row = document.createElement("div");
  row.setAttribute("id", room.ID_room);    
  row.setAttribute("name", room.name);
  row.innerHTML = room.name;
  row.addEventListener("click", connectRoom);

  container.appendChild(row);
})
```

- <code>document.getElementById("roomList")</code>  restituisce l'elemento con l'ID specificato, in questo caso un div.
- <code>document.createElement("div")</code> crea un div
- <code>row.setAttribute("id", room.ID_room)</code> imposta l'attributo ID dell'elemento con il campo ID_room del JSON.
- <code>row.innerHTML = room.name</code> assegna il testo all'elemento
- <code>row.addEventListener("click", connectRoom)</code> aggiunge un listener per l'evento click, gestito dalla funzione connectRoom
- <code>container.appendChild(row)</code> infine il div appena creato viene aggiunto al div che abbiamo ottenuto all'inizio del codice

### 5.1.3 AJAX
AJAX è un insieme di tecnologie che permette lo scambio di dati asincrono tra client e server. Viene utilizzato in questo progetto per quasi tutte le comunicazioni col server, tramite il metodo <bold>$.ajax()</bold> della libreria JQuery.

Esempio, login() di login.js:

```js
$.ajax({
        url: "../backend/login.php",
        method: "post",
        data:{
            username:username,
            password:password,
        }
      }).done(function(message) {
            ...
        });
```
Il metodo prende come parametro un oggetto con i seguenti attributi:
- url: URL della pagina verso cui effettuare la richiesta.
- method: metodo HTTP da utilizzare.
- data: oggetto con i dati da mandare.

Viene poi richiamato il metodo <bold>done()</bold> dell'oggetto ritornato da ajax(). Questo metodo prende come parametro una funzione da eseguire quando il server risponde. Il parametro di quest'ultima funzione è la risposta del server.



## 5.2 PHP
PHP è il linguaggio di scripting server-side utilizzato per interagire con il client e con il database.

Di seguito verranno presentati gli script principali e le tecniche usate più frequentemente.

### 5.2.1 conn.php
Questo script viene incluso in ogni altro script che preveda di utilizzare il database. Viene infatti inizializzata la variabile $conn, rappresentante la connessione al database.
```php
$servername = "127.0.0.1:3306";
$username = "root";
$password = "";
$database = "hangman";

$conn = new mysqli($servername, $username, $password, $database);
```

### 5.2.2 settings.php
In questo file vengono effettuate chiamate alle funzioni che modificano le impostazioni dello script in esecuzione. In particolare, le chiamate:

```php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
```

Permettono di stampare gli errori incontrati durante l'esecuzione e lo startup dello script. Chiaramente vengono disattivati in un ambiente di produzione, poiché potrebbero rivelare informazioni sensibili.

Questo può essere ottenuto anche modificando il file php.ini, dove ci sono le impostazioni generali per l'intero server PHP.

```php
set_time_limit(0);
```
Questa funzione è molto importante in quanto rimuove un limite di tempo all'esecuzione di uno script (di default 120 secondi). Ciò è necessario perché non si chiudano in maniera anomala gli script a cui l'utente deve restare connesso per ottenere notifiche push dal server. 


### 5.2.3 Prepared Statements
Per le query vengono sfruttati per tutto il progetto i prepared statements, vantaggiose per motivi  di sicurezza in quanto effettuano automaticamente l'escape dei parametri forniti in base al charset specificato per la connessione (o quello di default).

Esempio:
```php
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
        
$row = $stmt->get_result()->fetch_assoc();
$password2 = $row['password'];
```
Viene preparata la query, viene fatto il binding dei parametri e infine la query viene eseguita.

Si ottiene poi il risultato come array associativo (in questo caso era implicito che il result set contenesse solo un elemento).

## 5.2.4 Server Sent Events
Server Sent Events (SSE) è una tecnologia che consente a un client di aprire un canale di comunicazione unidirezionale con un server, e ottenerne periodicamente degli aggiornamenti.

Il mime type per SSE è text/event-stream, e viene specificato dal server tramite la funzione header(). In JavaScript esiste l'oggetto EventSouce che permette di aprire una connessione persistente verso un server SSE.

Prenderemo come esempio la gestione della chat, analizzando prima il lato server:

```php
    . . .
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    . . .
```
Vengono effettuati i dovuti controlli (in questo caso controllare se l'utente è connesso alla stanza) e viene poi aperto lo stream.


```php
    while(1){
                ...
                echo "data:".json_encode($message)."\n\n";
                ...
    }
```
Successivamente viene aperto un ciclo while infinito che ogni secondo controlla se sono arrivati nuovi messaggi, e in caso ci siano li manda al client. Il messaggio deve essere preceduto da "data: " e seguito da "\n\n". Sarebbe inoltre possibile specificare di che tipo di evento si tratta, ma in questo caso non è necessario e quindi arrivano come evento di tipo "message".

Lato client invece:
```js
const chatSource = new EventSource('../backend/chat.php?roomID='+roomID);
chatSource.addEventListener("message", function(e) {
            writeChatMessage(JSON.parse(e.data));
       });
```
Apriamo una connessione verso il server, e aggiungiamo all'EventSource un listener all'evento message.

## 5.3 Interfaccia Grafica

## 5.4 Pagine
In questa sezione verranno illustrate tutte le pagine web che compongono il sito e il loro funzionamento
### 5.4.1 Signup
In questa pagina l'utente può registrarsi al sito, fornendo un username, password e conferma password tramite un form. In caso ci siano errori, verranno mostrati.

Gli errori potrebbero essere:
 - Username, password e/o conferma password non forniti.
 - Esiste già un account con lo stesso username
 - Password e conferma password non coincidono
 - Errore generico del server

Una volta premuto il bottone di registrazione parte l'esecuzione della funzione signup() che legge i campi dal form:
```js
let form = $("#signupForm");

let username = form.find("[name='username']").val(); 
let password = form.find("[name='password']").val(); 
let confirmPassword = form.find("[name='confirmPassword']").val(); 
```

manda una richiesta HTTP POST alla risorsa user (si noti che per questa e tutte le chiamate che involgono le API, il campo data è una stringa JSON):
```js
$.ajax({
        url: "../backend/restAPI/userAPI.php",
        method: "post",
        data:JSON.stringify({
            username:username,
            password:password,
            confirmPassword:confirmPassword
        })
```
Se lo status della risposta è <bold>success</bold> l'utente viene reindirizzato alla home page.
Altrimenti viene costruito il div degli errori.
```js
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if(message.status == "success"){
                window.location = "../html/userHome.html";
            }
            else if(message.status == "not_valid"){
                let errors = message.payload;
                var container = document.getElementById("errors");

                errors.forEach(function (error){
                    var row = document.createElement("div");
                    row.innerHTML = error;
                    container.appendChild(row);
                })
            }
            else if(message.status == "error"){
                var container = document.getElementById("errors");
                var row = document.createElement("div");
                row.innerHTML = "Errore del server";
                container.appendChild(row);
            }
        });
```

### 5.4.2 Login
In questa pagina l'utente può effettuare il login al sito.
Una volta premuto il bottone di registrazione parte l'esecuzione della funzione login(), che manda una richiesta HTTP POST allo script login.php.

Qui viene preso il record dell'utente che corrisponde all'username fornito, e viene confrontata la colonna password con l'hash della password inviata.

```php
$username = str_replace(" ","", $_POST["username"]);
$password1 = ($_POST["password"]);
```
Vengono inizializzate le variabili prendendole dall'array superglobale $_POST.
Dall'username vengono tolti gli spazi perché ciò viene fatto anche in fase di login.

```php
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
        
$row = $stmt->get_result()->fetch_assoc();
$password2 = $row['password'];
```


```php
if(password_verify($password1, $password2)){
  $status = "success";
  $_SESSION['userID'] = $row['ID_user'];
  $_SESSION['role'] = $row['role'];
  $_SESSION['username'] = $row['username'];
}
``` 
Per il controllo della password viene utilizzata la funzione built-in di php "password_verify()".
Se il controllo va a buon fine viene settato a "success" il messaggio di ritorno verso il client, e vengono inizializzati i campi della sessione.

### 5.4.3 userHome
A questa pagina si viene indirizzati dopo il login o la registrazione.
Da qui è possibile:
- Accedere alla pagina per creare una stanza
- Accedere alla lista delle stanze
- Effettuare il logout

I bottoni per la creazione stanza e per la lista stanze si trovano ognuno dentro un form, una volta premuti semplicemente mandano una richiesta GET alle pagine corrispondenti, che vengono caricate.

Il bottone logout invece ha un listener associato che manda una richiesta allo script logout.php e reindirizza la finestra alla pagina login.html

### 5.4.4 roomList
Questa è la pagina con la lista di tutte le stanze aperte, a cui l'utente può accedere con un click.

La lista delle stanze si trova all'interno di un div.
```html 
    <div id="roomList"></div>
```
Il div viene popolato dalla funzione buildTable, chiamata al caricamento della pagina.

Prima di tutto effettua una richiesta HTTP GET alla risorsa Room, specificando il parametro url "?status=open".
```js
$.ajax({
        url: "../backend/restAPI/roomAPI.php",
        method: "get",
        data:{
            status:"open"
        }
      })
```
Il risultato è una lista di stanze, ognuna delle quali viene inserita nella lista stanza come un div cliccabile.
```js
.done(function(message) {
            message = JSON.parse(message);
            if (message.status == "success"){
                message.payload.forEach(function (room) {
                    var container = document.getElementById("roomList");
                    
                    var row = document.createElement("div");
                    row.setAttribute("id", room.ID_room);    
                    row.setAttribute("name", room.name);
                    row.innerHTML = room.name;
                    row.addEventListener("click", connectRoom);

                    container.appendChild(row);
                });
            }
        });
```
Per ognuno dei div stanza creati viene specificato un ID, il nome, e viene aggiunto un listener per l'evento click, che invoca la funzione connectRoom:

```js
function connectRoom(){
    roomID = this.id;
    $.ajax({
        url: "../backend/roomConnection.php",
        method: "POST",
        data: {
            roomID: roomID
        }
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if (message.status == "success"){
                window.location = "../html/room.html?roomID=" + roomID;
            }
            else if (message.status == "closed"){
                $("#error").html("La stanza è chiusa");
                document.getElementById(roomID).innerHTML = "";
            }
            else if (message.status == "closed"){
                $("#error").html("Errore del server");
            }
        });
}
```
Lo script roomConnection.php controlla se la stanza è aperta e inserisce la coppia (userID,roomID) nella relazione room_partecipation. 

Nel caso la stanza sia chiusa, viene tornato un messaggio d'errore, in caso contrario viene aperta la pagina della stanza cliccata.

### 5.4.5 createRoom
In questa pagina è presente un pulsante per tornare alla home, e un form per creare la stanza.

L'utente deve fornire il nome della stanza, che deve essere non nullo e univoco. Al click del bottone parte una richiesta HTTP POST alla risorsa Room. In caso di successo, l'utente viene portato alla pagina della stanza appena creata, in caso contrario viene stampato un errore.

### 5.4.6 room
queryparam, connessione alla stanza e alla chat, form di chat, form di creazione partita per il creatore, connessione alla partita, form di tentativo.


# 6 Servizi
In questa sezione verranno documentate le API per accedere alle varie risorse.

Le API si basano sui principi architetturali REST, quindi le risorse sono identificate da un URI e le operazioni CRUD sono mappate ai metodi HTTP.

Prima di svolgere alcune operazioni viene verificato se chi le sta effettuando ha abbastanza permessi.

Il body di ogni richiesta, se presente, deve essere in formato JSON.

Per tutte le risorse il server risponde con un JSON con i campi:
  - status, che si suddivide in:
    - not_valid: i valori di input sono malformati. Nel payload viene descritto quale sia l'errore
    - success: la richiesta è andata a buon fine. Il payload è diverso in base al tipo di richiesta
    - denied: permessi insufficienti
    - error: errore generico del server
  - payload, utilizzato per i dati che il server deve comunicare. In caso di successo il significato cambia in base al tipo di richiesta:
    - GET: JSON con i dati delle risorse richieste
    - POST: ID della risorsa appena inserita
    - PUT e DELETE: numero di record modificati
    
## 6.1 Room
<code>GET /roomAPI.php/ID</code>
- <code>"payload":[{"ID_room":ID,"name":name}]</code>

<code>GET /roomAPI.php</code>

<code>GET /roomAPI.php?status=open</code>
- Ritorna solo le stanze aperte


<code>POST /roomAPI.php
{
roomname:roomname
}</code>
- Solo un utente loggato può creare una stanza

<code>PUT /roomAPI.php/ID
{
roomname:roomname
}
</code>
- Solo il proprietario di una stanza può modificarne il nome

<code>DELETE /roomAPI.php/ID</code>
- Solo un admin può eliminare una stanza

## 6.2 User
<code>GET /userAPI.php/ID</code>
- <code>"payload":[{"ID_user":ID,"username":username}]</code>

<code>GET /userAPI.php</code>


<code>POST /userAPI.php
{
username:username,
password:password,
confirmPassword:confirmPassword
}</code>


<code>PUT /userAPI.php/ID
{
username:username
}
</code>
- Solo un utente può modificare il suo stesso username

<code>DELETE /userAPI.php/ID</code>
- Solo un utente può eliminare il suo stesso account
## 6.3 Word
Solo un admin può effettuare le seguenti richieste

<code>GET /wordAPI.php/ID</code>
- <code>"payload":[{"ID_word":4,"word":"ciao"}</code>

<code>GET /wordAPI.php</code>


<code>POST /wordAPI.php
{
word:word
}</code>


<code>PUT /wordAPI.php/ID
{
word:word
}
</code>


<code>DELETE /wordAPI.php/ID</code>
