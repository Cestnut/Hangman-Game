# 1 Idea

Il progetto si basa sul gioco dell'impiccato, in cui i giocatori hanno un certo numero di tentativi per indovinare una parola, e per ogni tentativo gli viene indicato quanto vicini o meno erano alla soluzione.

## 1.1 Modalità di gioco
Sono previste le seguenti modalità di gioco:
- **Cooperativa**, i giocatori hanno vite condivise, ricevono gli stessi indizi e a turno provano a indovinare la parola. Ogni turno ha una durata limitata.
- **Competitiva**, i giocatori devono indovinare la stessa parola ma sono isolati tra di loro. Una volta che tutti hanno finito viene stilata una classifica. Ogni giocatore ha tempo limitato per indovinare.


La partita può essere ulteriormente categorizzata in base a come viene scelta la parola da indovinare:
- **Dal server**, la parola viene scelta dal server da una lista di parole
- **Da un giocatore**, la parola viene scelta da un utente selezionato dall'host della stanza.

## 1.2 Stanze
### 1.2.1 Gestione
Ogni utente al menù principale ha la possibilità di creare una stanza o unirsi a una già esistente. Alla creazione di una stanza l'utente deve fornire:
- nome stanza (suggerito nomeutente's room)
- password (facoltativa)
- numero massimo di giocatori
- modalità di gioco
- modalità di scelta della parola
- tempo massimo di ogni giocatore
- vite massime
In ogni caso il creatore della stanza può cambiare queste impostazioni dopo averla creata e chi sia a scegliere la parola in caso debba essere scelta da un giocatore.

### 1.2.2 Chat
All'interno di ogni stanza di gioco è disponibile una chat in cui i giocatori possono comunicare; chi può accedere dipende dalla modalità di gioco:
- **Cooperativa**, disponibile sia durante la partita che dopo
- **Competitiva**, disponibile solo per chi ha già indovinato la parola
Ogni messaggio è accompagnato dal mittente e dalla data e ora di invio.

## 1.3 Profilo utente
Ogni giocatore ha un profilo in cui sono visibili le seguenti informazioni:
- **Nome utente**
- **Data iscrizione**
- **Partite giocate**
- **Partite vinte**

# 2 Risorse

Saranno implementate REST API per interagire con le seguenti risorse:
- **Utenti**
- **Partite**
- **Lista parole**

# 3 Gioco
Verrà ora specificato come si svolgono le partite e come vengono mostrati i punteggi in base alle modalità. Al termine della partita viene mostrato quale fosse la parola giusta e viene salvata insieme ai punteggi.

## 3.1 Cooperativa
### 3.1.1 Svolgimento
Viene scelto l'ordine dei giocatori, che a ogni turno prova a indovinare la parola. In caso sia sbagliata viene persa una vita per tutti. Se il turno scade semplicemente si passa al giocatore successivo.
### 3.1.2 Calcolo punteggio
Viene mostrato il tempo impiegato per terminare la partita e in caso di vittoria il numero di tentativi. Il risultato viene salvato sulla partita.

## 3.2 Competitiva
### 3.2.1 Svolgimento
Ogni giocatore prova a indovinare la parola singolarmente, senza vedere il progresso degli altri. Se finisce le vite o scade il tempo, perde.
### 3.2.2 Calcolo punteggio
Viene mostrato per ogni giocatore il tempo impiegato e il numero di tentativi. Il risultato viene salvato sulla partita.
