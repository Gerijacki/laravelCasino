<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlackjackController extends Controller
{
    public function index()
    {
        // Obtener el saldo del usuario
        $user = auth()->user();
        return view('blackjack.index', ['user' => $user, 'bet' => 0, 'playerHand' => [], 'dealerHand' => [], 'playerTotal' => 0, 'dealerTotal' => 0]);
    }

    public function start(Request $request)
    {
        $user = auth()->user();
        $bet = $request->input('bet');

        if ($bet > $user->balance) {
            return redirect()->route('blackjack.index')->with('error', 'Apuesta mayor que el saldo disponible.');
        }

        // Barajar y repartir cartas
        $deck = $this->shuffleDeck();
        $playerHand = [$this->dealCard($deck), $this->dealCard($deck)];
        $dealerHand = [$this->dealCard($deck), $this->dealCard($deck)];

        $playerTotal = $this->calculateHandValue($playerHand);
        $dealerTotal = $this->calculateHandValue($dealerHand);

        return view('blackjack.game', [
            'user' => $user,
            'bet' => $bet,
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerTotal' => $playerTotal,
            'dealerTotal' => $dealerTotal,
            'deck' => $deck
        ]);
    }

    public function hit(Request $request)
    {
        $deck = json_decode($request->input('deck'), true);
        $playerHand = json_decode($request->input('player_hand'), true);

        // Repartir carta al jugador
        $playerHand[] = $this->dealCard($deck);
        $playerTotal = $this->calculateHandValue($playerHand);

        // Si el jugador se pasa de 21, termina el juego
        if ($playerTotal > 21) {
            return redirect()->route('blackjack.result', [
                'resultMessage' => 'Dealer wins',
                'user' => auth()->user(),
                'playerHand' => $playerHand,
                'dealerHand' => json_decode($request->input('dealer_hand'), true),
                'playerTotal' => $playerTotal,
                'dealerTotal' => json_decode($request->input('dealer_total'))
            ]);
        }

        return view('blackjack.game', [
            'user' => auth()->user(),
            'bet' => $request->input('bet'),
            'playerHand' => $playerHand,
            'dealerHand' => json_decode($request->input('dealer_hand'), true),
            'playerTotal' => $playerTotal,
            'dealerTotal' => json_decode($request->input('dealer_total')),
            'deck' => $deck
        ]);
    }

    public function stand(Request $request)
    {
        $deck = json_decode($request->input('deck'), true);
        $playerHand = json_decode($request->input('player_hand'), true);
        $dealerHand = json_decode($request->input('dealer_hand'), true);
        $playerTotal = $request->input('player_total');
        $bet = $request->input('bet');

        // Mostrar las cartas del dealer con un pequeño retraso
        $dealerTotal = $this->calculateHandValue($dealerHand);
        $dealerMoves = [];
        foreach ($dealerHand as $card) {
            $dealerMoves[] = $card;
            usleep(1000000); // Retardo de 1 segundo entre cartas
        }

        // El dealer toma cartas hasta alcanzar 17
        while ($dealerTotal < 17) {
            $dealerHand[] = $this->dealCard($deck);
            $dealerMoves[] = end($dealerHand);
            $dealerTotal = $this->calculateHandValue($dealerHand);
            usleep(1000000); // Retardo de 1 segundo entre cartas
        }

        $resultMessage = $this->getGameResult($playerTotal, $dealerTotal);

        // Actualizar el saldo del usuario
        $user = auth()->user();
        if ($resultMessage == 'Player wins') {
            $user->balance += $bet;
        } elseif ($resultMessage == 'Dealer wins') {
            $user->balance -= $bet;
        }

        //$user->save();

        return view('blackjack.result', [
            'user' => $user,
            'playerHand' => $playerHand,
            'dealerMoves' => $dealerMoves,
            'playerTotal' => $playerTotal,
            'dealerTotal' => $dealerTotal,
            'resultMessage' => $resultMessage
        ]);
    }

    private function shuffleDeck()
    {
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        $deck = [];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = ['suit' => $suit, 'rank' => $value, 'image' => "images/{$value}_of_{$suit}.png"];
            }
        }

        shuffle($deck);
        return $deck;
    }

    private function dealCard(&$deck)
    {
        return array_pop($deck);
    }

    private function calculateHandValue($hand)
    {
        $total = 0;
        $aces = 0;

        foreach ($hand as $card) {
            if (is_numeric($card['rank'])) {
                $total += (int) $card['rank'];
            } elseif ($card['rank'] == 'A') {
                $aces++;
                $total += 11;
            } else {
                $total += 10;
            }
        }

        while ($total > 21 && $aces > 0) {
            $total -= 10;
            $aces--;
        }

        return $total;
    }

    private function getGameResult($playerTotal, $dealerTotal)
    {
        if ($playerTotal > 21) {
            return 'Dealer wins';
        } elseif ($dealerTotal > 21 || $playerTotal > $dealerTotal) {
            return 'Player wins';
        } elseif ($playerTotal < $dealerTotal) {
            return 'Dealer wins';
        } else {
            return 'Draw';
        }
    }
}
