# 1 Idea

Il progetto si basa sul gioco dell'impiccato, in cui i giocatori hanno un certo numero di tentativi per indovinare una parola scelta dal server da una lista predefinita, e per ogni tentativo gli viene indicato quanto vicini o meno erano alla soluzione.

## 1.1 Modalità di gioco
Sono previste le seguenti modalità di gioco:
- **Cooperativa**, i giocatori hanno vite condivise, ricevono gli stessi indizi e a turno provano a indovinare la parola. Ogni turno ha una durata limitata.
- **Competitiva**, i giocatori devono indovinare la stessa parola ma sono isolati tra di loro. Una volta che tutti hanno finito viene stilata una classifica. Ogni giocatore ha tempo limitato per indovinare.

## 1.2 Stanze
### 1.2.1 Gestione
Ogni utente al menù principale ha la possibilità di creare una stanza o unirsi a una già esistente. Alla creazione di una stanza l'utente deve fornire:
- nome stanza (suggerito nomeutente's room)
- password (facoltativa)
- numero massimo di giocatori
- modalità di gioco
- tempo massimo di ogni giocatore
- vite massime

### 1.2.2 Chat
All'interno di ogni stanza di gioco è disponibile una chat in cui i giocatori possono comunicare, ogni messaggio è accompagnato dal nome del mittente.

# 2 Risorse
Saranno implementate REST API per interagire con le seguenti risorse:
- **Utenti**
- **Partite**
- **Lista parole**

# 3 Gioco
Verrà ora specificato come si svolgono le partite. Al termine della partita viene mostrato quale fosse la parola giusta.

## 3.1 Cooperativa
### 3.1.1 Svolgimento
Viene scelto l'ordine dei giocatori, che a ogni turno provano a indovinare la parola. In caso sia sbagliata viene persa una vita per tutti. Se il turno scade semplicemente si passa al giocatore successivo.

## 3.2 Competitiva
### 3.2.1 Svolgimento
Ogni giocatore prova a indovinare la parola singolarmente, senza vedere il progresso degli altri. Se finisce le vite o scade il tempo, perde.

Al termine della partita, viene mostrata una classifica in base a chi ha indovinato per primo.
