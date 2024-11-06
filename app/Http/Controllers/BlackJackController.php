<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlackjackController extends Controller
{
    // Función para mostrar la página del juego
    public function index()
    {
        return view('blackjack.index', ['user' => Auth::user()]);
    }

    // Función para iniciar una partida de Blackjack
    public function startGame(Request $request)
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        $bet = $request->input('bet'); // Obtener la apuesta del jugador

        // Validar que el jugador tenga suficiente saldo
        if ($user->balance < $bet) {
            return redirect()->route('blackjack.index')->withErrors(['error' => 'Insufficient balance to place the bet.']);
        }

        // Crear un mazo de cartas (barajarlas)
        $deck = $this->createDeck();

        // Repartir las cartas
        $playerHand = $this->dealCards($deck);
        $dealerHand = $this->dealCards($deck);

        return view('blackjack.game', [
            'user' => $user,
            'bet' => $bet,
            'deck' => $deck,
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerTotal' => $this->calculateTotal($playerHand),
            'dealerTotal' => $this->calculateTotal($dealerHand),
        ]);
    }

    // Función para calcular el total de una mano
    public function calculateTotal($hand)
    {
        $total = 0;
        $aces = 0;

        foreach ($hand as $card) {
            if (in_array($card['rank'], ['2', '3', '4', '5', '6', '7', '8', '9', '10'])) {
                $total += (int)$card['rank'];
            } elseif (in_array($card['rank'], ['J', 'Q', 'K'])) {
                $total += 10;
            } elseif ($card['rank'] == 'A') {
                $total += 11;
                $aces++;
            }
        }

        while ($total > 21 && $aces > 0) {
            $total -= 10; // Convertir un As de 11 a 1
            $aces--;
        }

        return $total;
    }

    // Función para crear un mazo de cartas
    public function createDeck()
    {
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        $deck = [];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $imagePath = "images/cards/{$rank}_of_{$suit}.png"; 
                $imageUrl = asset($imagePath);

                if (!file_exists(public_path($imagePath))) {
                    $imageUrl = asset("images/cards/default.png"); 
                }

                $deck[] = [
                    'rank' => $rank,
                    'suit' => $suit,
                    'image' => $imageUrl,
                ];
            }
        }

        shuffle($deck); // Barajar el mazo
        return $deck;
    }

    // Función para permitir al jugador pedir una carta más
    public function hit(Request $request)
    {
        $playerHand = json_decode($request->input('player_hand'), true);
        $deck = json_decode($request->input('deck'), true);

        if (!is_array($playerHand) || !is_array($deck)) {
            return redirect()->route('blackjack.index')->withErrors(['error' => 'Invalid data received.']);
        }

        // Repartir una carta al jugador
        $randomCardIndex = array_rand($deck); 
        $randomCard = $deck[$randomCardIndex]; 
        unset($deck[$randomCardIndex]); 

        $playerHand[] = $randomCard;

        $playerTotal = $this->calculateTotal($playerHand);

        // Verificar si el jugador se pasa de 21
        if ($playerTotal > 21) {
            return $this->endGame('You busted! You lose.', -$request->input('bet'));
        }

        return view('blackjack.game', [
            'playerHand' => $playerHand,
            'deck' => array_values($deck),
            'playerTotal' => $playerTotal,
            'dealerHand' => $request->input('dealer_hand'),
            'dealerTotal' => $request->input('dealer_total'),
            'user' => Auth::user(),
            'bet' => $request->input('bet')
        ]);
    }

    // Función para el turno de la banca
    public function dealerTurn(Request $request)
    {
        $dealerHand = $request->input('dealer_hand');
        $deck = $request->input('deck');

        // La banca debe pedir cartas hasta tener al menos 17 puntos
        while ($this->calculateTotal($dealerHand) < 17) {
            $randomCardIndex = array_rand($deck); 
            $randomCard = $deck[$randomCardIndex]; 
            unset($deck[$randomCardIndex]); 
            $dealerHand[] = $randomCard;
        }

        $dealerTotal = $this->calculateTotal($dealerHand);

        // Determinar el ganador
        return $this->determineWinner($request->input('player_total'), $dealerTotal, $request->input('bet'));
    }

    // Función para determinar el ganador
    public function determineWinner($playerTotal, $dealerTotal, $bet)
    {
        $user = Auth::user();

        if ($playerTotal > 21) {
            return $this->endGame('You busted! You lose.', -$bet);
        } elseif ($dealerTotal > 21) {
            return $this->endGame('Dealer busted! You win.', $bet);
        } elseif ($playerTotal > $dealerTotal) {
            return $this->endGame('You win!', $bet);
        } elseif ($playerTotal < $dealerTotal) {
            return $this->endGame('Dealer wins.', -$bet);
        } else {
            return $this->endGame('It\'s a tie!', 0);
        }
    }

    // Función para finalizar el juego y actualizar el balance
    private function endGame($message, $balanceChange)
    {
        $user = Auth::user();
        $user->balance += $balanceChange;

        return redirect()->route('blackjack.index')->with('message', $message);
    }

    // Función para repartir cartas
    public function dealCards(&$deck)
    {
        $hand = [];
        $hand[] = array_pop($deck);
        $hand[] = array_pop($deck);

        return $hand;
    }
}
