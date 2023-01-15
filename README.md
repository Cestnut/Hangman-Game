# 1 Idea yo

Il progetto prevede la creazione di una versione online del gioco dell'impiccato. I giocatori avranno a disposizione un certo numero di tentativi per indovinare una parola scelta dal server, tra quelle presenti in una lista predefinita. Dopo ogni tentativo, il giocatore verrà informato su quanto la propria risposta si avvicini alla soluzione esatta.

## 1.1 Modalità di gioco
I giocatori condividono Vite e Indizi, a turno provano a indovinare la parola. Ogni turno ha una durata limitata.

## 1.2 Stanze
### 1.2.1 Gestione
Ogni utente al menù principale ha la possibilità di creare una stanza o unirsi a una già esistente. Alla creazione di una stanza l'utente deve fornire:
- nome stanza (suggerito nomeutente's room)

Il creatore di una stanza all'interno della stanza, prima di iniziare una partita deve fornire:
- tempo massimo per il turno di ogni giocatore
- vite massime

Può premere il pulsanto di avvio per far iniziare la partita.

Se il creatore della stanza esce, la stanza si chiude

Un utente non può unirsi alla stanza mentre una partita è in corso.

### 1.2.2 Chat
All'interno di ogni stanza di gioco è disponibile una chat in cui i giocatori possono comunicare, ogni messaggio è accompagnato dal nome del mittente.

# 2 Risorse
Saranno implementate REST API per interagire con le seguenti risorse:
- **User**
- **Room**
- **Wordlist**

# 3 Gioco
Viene scelto l'ordine dei giocatori, che a ogni turno provano a indovinare la parola. In caso sia sbagliata viene persa una vita per tutti. Se il turno scade semplicemente si passa al giocatore successivo.


# 4 Database
Per il salvataggio dei dati Il gioco si basa su un database SQL. Di seguito verranno mostrati il diagramma ER e lo schema relazionale.
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
## 5.1 Tecnologie
Per la realizzazione dell'applicazione sono stati usati HTML, CSS, PHP, Javascript (utilizzando anche la libreria jQuery in particolar modo per le chiamate Ajax).
Client e server comunicano tramite chiamate Ajax. In alcuni casi è necessario ci sia uno stream tra client e server per sincronizzare i client, per far ciò si utilizzano i Server Side Events (SSE).

## 5.2 Pagine
In questa sezione verranno illustrate tutte le pagine web che compongono il sito e il loro funzionamento
### 5.2.1 Login
### 5.2.2 Signup
### 5.2.3 userHome
### 5.2.4 roomList
### 5.2.5 createRoom

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
  - payload (opzionale), utilizzato per i dati che il server deve comunicare. In caso di successo il significato cambia in base al tipo di richiesta:
    - GET: JSON con i dati delle risorse richieste
    - POST: ID della risorsa appena inserita
    - PUT e DELETE: numero di record modificati
    
## 6.1 Room
### 6.1.1 GET
Tramite il metodo GET è possibile:
- Specificare l'ID della risorsa e ottenere un JSON con ID e nome della stanza.
- Non specificare un ID e ottenere un JSON con ID e nome di tutte le stanze, o di tutte le stanze aperte se si specifica il query parameter status=open.

### 6.1.2 POST
Tramite il metodo POST è possibile creare una stanza, a condizione che l'utente sia loggato, inserendo nel body della richiesta il nome della stanza.

Verrà inoltre automaticamente inserito il record (ID_room, ID_host) all'interno della tabella Room_Partecipation.

Viene controllato che l'utente sia loggato prima di svolgere alcuna operazione.

Se l'utente è loggato viene tornato in output l'ID della stanza appena creata. In caso contrario verrà ritornata una stringa vuota.
### 6.1.3 PUT
Tramite il metodo PUT viene aggiornato il nome della stanza, a condizione che l'utente che sta cercando di eseguire l'operazione sia il creatore della stanza.

Viene tornato in output il numero di righe modificate in caso l'utente abbia abbastanza permessi, una stringa vuota in caso contrario.
### 6.1.4 DELETE
Tramite questo metodo viene eliminata la stanza dal database, a condizione che l'operazione venga effettuata da un admin.
