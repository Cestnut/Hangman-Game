# 1 Idea

Il progetto si basa sul gioco dell'impiccato, in cui i giocatori hanno un certo numero di tentativi per indovinare una parola, e per ogni tentativo gli viene indicato quanto vicini o meno erano alla soluzione.

Sono previste le seguenti modalità di gioco:
- **Cooperativa**, i giocatori hanno vite condivise, ricevono gli stessi indizi e a turno provano a indovinare la parola. Ogni turno ha una durata limitata.
-  **Competitiva**, i giocatori devono indovinare la stessa parola ma sono isolati tra di loro. Una volta che tutti hanno finito viene stilata una classifica. Ogni giocatore ha tempo limitato per indovinare.

Sono previste le seguenti tipologie di partita:
- **Online**, matchmaking avviene online con giocatori casuali e la parola viene scelta da una lista predefinita
- **Modalità personalizzata**, vengono create delle stanze private o pubbliche, in cui è possibile scegliere la parola dalla lista del server o farla scegliere da qualcuno selezionato dall'host della stanza.

All'interno di ogni stanza di gioco è disponibile una chat in cui i giocatori possono comunicare; chi può accedere dipende dalla modalità di gioco:
- **Cooperativa**, disponibile sia durante la partita che dopo
- **Competitiva**, disponibile solo per chi ha già indovinato la parola

Ogni giocatore ha un profilo in cui sono visibili le seguenti informazioni:
- **Nome utente**
- **Data iscrizione**
- **Partite giocate**, solo contro il server
- **Partite vinte**, solo contro il server

# 2 Risorse

Saranno implementate REST API per interagire con le seguenti risorse:
- **Utenti**
- **Partite**
- **Lista parole** 
