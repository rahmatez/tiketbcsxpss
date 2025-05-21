<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View as ViewFacade;
use Carbon\Carbon;

class GameController extends Controller
{
    // Show games & game detail for frontend
    public function index(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $games = Game::orderBy('match_time', 'desc')->get();
        return view('home', compact('games'));
    }

    public function show($id): View|Application|Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $game = Game::findOrFail($id);
        $tickets = Ticket::where('game_id', $id)->get();
        
        // Jika tidak ada tiket, redirect ke halaman utama dengan pesan
        if ($tickets->isEmpty()) {
            return redirect()->route('home')->with('error', 'Tidak ada tiket tersedia untuk pertandingan ini.');
        }

        $purchasedQuantities = DB::table('orders')
            ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
            ->select('tickets.category', DB::raw('SUM(orders.quantity) as total_purchased'))
            ->where('tickets.game_id', $id)
            ->groupBy('tickets.category')
            ->pluck('total_purchased', 'category');

        // Pastikan ada kategori default yang valid
        $defaultCategory = $tickets->first()->category;

        return view('game_detail', [
            'game' => $game,
            'tickets' => $tickets,
            'purchasedQuantities' => $purchasedQuantities,
            'defaultCategory' => $defaultCategory
        ]);
    }

    // Admin: Game management
    
    // List all games for admin
    public function adminIndex(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $gamesQuery = Game::query();
        
        // Filter by status if provided
        if (request()->has('status') && request('status') !== 'all') {
            $gamesQuery->where('status', request('status'));
        }
        
        // Filter by type (home/away)
        if (request()->has('type')) {
            if (request('type') === 'home') {
                $gamesQuery->where('is_home_game', 1);
            } elseif (request('type') === 'away') {
                $gamesQuery->where('is_home_game', 0);
            }
        }
        
        // Search by team names
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $gamesQuery->where(function($query) use ($search) {
                $query->where('home_team', 'like', "%{$search}%")
                      ->orWhere('away_team', 'like', "%{$search}%")
                      ->orWhere('tournament_name', 'like', "%{$search}%");
            });
        }
        
        $games = $gamesQuery->orderBy('match_time', 'desc')->paginate(15);
        
        // Get sales data for home games
        $homeGameIds = $games->where('is_home_game', 1)->pluck('id')->toArray();
        
        if (!empty($homeGameIds)) {
            $ticketSalesData = DB::table('tickets')
                ->leftJoin('orders', 'tickets.id', '=', 'orders.ticket_id')
                ->select('tickets.game_id', 
                         DB::raw('SUM(IFNULL(orders.quantity, 0)) as sold_tickets'))
                ->whereIn('tickets.game_id', $homeGameIds)
                ->groupBy('tickets.game_id')
                ->pluck('sold_tickets', 'tickets.game_id');
        } else {
            $ticketSalesData = collect();
        }
        
        return view('admin.games.index', compact('games', 'ticketSalesData'));
    }

    //Create game & ticket
    public function create(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.games.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'home_team' => 'required|string|max:255',
            'away_team' => 'required|string|max:255',
            'match_time' => 'required|date',
            'is_home_game' => 'required|boolean',
            'tournament_name' => 'required|string|max:255',
            'purchase_deadline' => 'required|date',
            'stadium_name' => 'required|string|max:255',
            'status' => 'required|string|in:upcoming,ongoing,completed,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'home_team_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'away_team_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->is_home_game == 1) {
            // Validate ticket fields only if they are provided
            // This allows submitting the form with empty ticket fields
            $request->validate([
                'tribun_selatan_ticket_quantity' => 'nullable|integer|min:0',
                'tribun_selatan_ticket_price' => 'nullable|integer|min:0',
                'tribun_utara_ticket_quantity' => 'nullable|integer|min:0',
                'tribun_utara_ticket_price' => 'nullable|integer|min:0',
                'tribun_timur_ticket_quantity' => 'nullable|integer|min:0',
                'tribun_timur_ticket_price' => 'nullable|integer|min:0',
                'tribun_barat_ticket_quantity' => 'nullable|integer|min:0',
                'tribun_barat_ticket_price' => 'nullable|integer|min:0',
            ]);
        }

        // Create game
        $game = new Game;
        $game->home_team = $request->home_team;
        $game->away_team = $request->away_team;
        $game->match_time = $request->match_time;
        $game->is_home_game = $request->is_home_game;
        $game->tournament_name = $request->tournament_name;
        $game->purchase_deadline = $request->purchase_deadline;
        $game->stadium_name = $request->stadium_name;
        $game->status = $request->status;
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('games', 'public');
            $game->image_path = $imagePath;
        }
        
        // Handle home team logo upload
        if ($request->hasFile('home_team_logo')) {
            $homeTeamLogoPath = $request->file('home_team_logo')->store('team_logos', 'public');
            $game->home_team_logo = $homeTeamLogoPath;
        }
        
        // Handle away team logo upload
        if ($request->hasFile('away_team_logo')) {
            $awayTeamLogoPath = $request->file('away_team_logo')->store('team_logos', 'public');
            $game->away_team_logo = $awayTeamLogoPath;
        }
        
        $game->save();

        if ($request->is_home_game == 1) {
            // Create tickets for all categories, even if form was left empty (will use 0 as default)
            $ticketCategories = [
                ['name' => 'Tribun Selatan', 'quantity' => $request->tribun_selatan_ticket_quantity, 'price' => $request->tribun_selatan_ticket_price],
                ['name' => 'Tribun Utara', 'quantity' => $request->tribun_utara_ticket_quantity, 'price' => $request->tribun_utara_ticket_price],
                ['name' => 'Tribun Timur', 'quantity' => $request->tribun_timur_ticket_quantity, 'price' => $request->tribun_timur_ticket_price],
                ['name' => 'Tribun Barat', 'quantity' => $request->tribun_barat_ticket_quantity, 'price' => $request->tribun_barat_ticket_price],
            ];
            
            $ticketsCreated = false;
            
            foreach ($ticketCategories as $ticketCategory) {
                // Always create ticket for home games, set to 0 if empty
                $ticket = new Ticket;
                $ticket->game_id = $game->id;
                $ticket->category = $ticketCategory['name'];
                $ticket->quantity = !empty($ticketCategory['quantity']) ? $ticketCategory['quantity'] : 0;
                $ticket->price = !empty($ticketCategory['price']) ? $ticketCategory['price'] : 0;
                $ticket->save();
                $ticketsCreated = true;
            }
        }

        // Display success message
        if ($request->is_home_game == 1) {
            return redirect()->route('admin.games.index')->with('success', 'Pertandingan dan tiket berhasil ditambahkan');
        } else {
            return redirect()->route('admin.games.index')->with('success', 'Pertandingan berhasil ditambahkan');
        }
    }
    
    // Edit game
    public function edit($id): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $game = Game::findOrFail($id);
        $tickets = Ticket::where('game_id', $id)->get();
        
        return view('admin.games.edit', compact('game', 'tickets'));
    }
    
    // Update game
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'home_team' => 'required|string|max:255',
            'away_team' => 'required|string|max:255',
            'match_time' => 'required|date',
            'tournament_name' => 'required|string|max:255',
            'purchase_deadline' => 'required|date',
            'stadium_name' => 'required|string|max:255',
            'status' => 'required|string|in:upcoming,ongoing,completed,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'home_team_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'away_team_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $game = Game::findOrFail($id);
        
        // Cannot change if game is home/away after creation
        $game->home_team = $request->home_team;
        $game->away_team = $request->away_team;
        $game->match_time = $request->match_time;
        $game->tournament_name = $request->tournament_name;
        $game->purchase_deadline = $request->purchase_deadline;
        $game->stadium_name = $request->stadium_name;
        $game->status = $request->status;
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($game->image_path && Storage::disk('public')->exists($game->image_path)) {
                Storage::disk('public')->delete($game->image_path);
            }
            
            $imagePath = $request->file('image')->store('games', 'public');
            $game->image_path = $imagePath;
        }
        
        // Handle home team logo upload
        if ($request->hasFile('home_team_logo')) {
            // Delete old logo if exists
            if ($game->home_team_logo && Storage::disk('public')->exists($game->home_team_logo)) {
                Storage::disk('public')->delete($game->home_team_logo);
            }
            
            $homeTeamLogoPath = $request->file('home_team_logo')->store('team_logos', 'public');
            $game->home_team_logo = $homeTeamLogoPath;
        }
        
        // Handle away team logo upload
        if ($request->hasFile('away_team_logo')) {
            // Delete old logo if exists
            if ($game->away_team_logo && Storage::disk('public')->exists($game->away_team_logo)) {
                Storage::disk('public')->delete($game->away_team_logo);
            }
            
            $awayTeamLogoPath = $request->file('away_team_logo')->store('team_logos', 'public');
            $game->away_team_logo = $awayTeamLogoPath;
        }
        
        $game->save();
        
        // If it's a home game, update ticket prices (not quantities)
        if ($game->is_home_game == 1 && $request->has('ticket_prices')) {
            foreach ($request->ticket_prices as $ticketId => $price) {
                $ticket = Ticket::findOrFail($ticketId);
                $ticket->price = $price;
                $ticket->save();
            }
        }
        
        return redirect()->route('admin.games.index')->with('success', 'Pertandingan berhasil diperbarui');
    }
    
    // Confirmation page for deleting game
    public function confirmDelete($id): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $game = Game::findOrFail($id);
        $orderCount = Order::where('game_id', $id)->count();
        $ticketCount = Ticket::where('game_id', $id)->count();
        
        return view('admin.games.delete', compact('game', 'orderCount', 'ticketCount'));
    }
    
    // Delete game
    public function destroy($id): RedirectResponse
    {
        // Debug logs
        Log::info('Game destroy method called with ID: ' . $id);
        
        try {
            $game = Game::findOrFail($id);
            Log::info('Game found: ' . $game->home_team . ' vs ' . $game->away_team);
            
            // Check if there are any orders for this game
            $orderCount = Order::where('game_id', $id)->count();
            Log::info('Order count for this game: ' . $orderCount);
            
            if ($orderCount > 0) {
                Log::info('Cannot delete game: has orders');
                return redirect()->route('admin.games.index')
                    ->with('error', 'Pertandingan tidak dapat dihapus karena sudah ada pesanan tiket');
            }
            
            // Delete associated tickets
            $ticketCount = Ticket::where('game_id', $id)->count();
            Log::info('About to delete ' . $ticketCount . ' tickets');
            Ticket::where('game_id', $id)->delete();
            
            // Delete image files if exists
            if ($game->image_path && Storage::disk('public')->exists($game->image_path)) {
                Storage::disk('public')->delete($game->image_path);
                Log::info('Image deleted: ' . $game->image_path);
            }
            
            // Delete home team logo if exists
            if ($game->home_team_logo && Storage::disk('public')->exists($game->home_team_logo)) {
                Storage::disk('public')->delete($game->home_team_logo);
                Log::info('Home team logo deleted: ' . $game->home_team_logo);
            }
            
            // Delete away team logo if exists
            if ($game->away_team_logo && Storage::disk('public')->exists($game->away_team_logo)) {
                Storage::disk('public')->delete($game->away_team_logo);
                Log::info('Away team logo deleted: ' . $game->away_team_logo);
            }
            
            // Delete the game
            Log::info('Deleting game with ID: ' . $id);
            $game->delete();
            Log::info('Game deleted successfully');
            
            return redirect()->route('admin.games.index')
                ->with('success', 'Pertandingan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting game: ' . $e->getMessage());
            return redirect()->route('admin.games.index')
                ->with('error', 'Terjadi kesalahan saat menghapus pertandingan: ' . $e->getMessage());
        }
    }
}
