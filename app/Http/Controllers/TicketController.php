<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function search(Request $request)
    {
        $query = Game::query();
          // Filter berdasarkan kata kunci
        if ($request->has('query') && !empty($request->input('query'))) {
            $searchQuery = $request->input('query');
            $query->where(function ($q) use ($searchQuery) {
                $q->where('home_team', 'like', "%{$searchQuery}%")
                  ->orWhere('away_team', 'like', "%{$searchQuery}%")
                  ->orWhere('tournament_name', 'like', "%{$searchQuery}%")
                  ->orWhere('stadium_name', 'like', "%{$searchQuery}%");
            });
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('date_range') && !empty($request->date_range)) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('match_time', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('match_time', Carbon::tomorrow());
                    break;
                case 'this_week':
                    $query->whereBetween('match_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('match_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
            }
        }
        
        // Filter berdasarkan tim
        if ($request->has('team') && !empty($request->team)) {
            $team = $request->team;
            $query->where(function ($q) use ($team) {
                $q->where('home_team', $team)
                  ->orWhere('away_team', $team);
            });
        }
        
        // Filter berdasarkan turnamen
        if ($request->has('tournament') && !empty($request->tournament)) {
            $query->where('tournament_name', $request->tournament);
        }
        
        // Filter berdasarkan lokasi (kandang/tandang)
        if ($request->has('location') && !empty($request->location)) {
            $query->where('is_home_game', $request->location == 'home' ? 1 : 0);
        }
        
        // Ambil data tim dan turnamen untuk dropdown filter
        $teams = Game::select('home_team')
            ->union(Game::select('away_team'))
            ->orderBy('home_team')
            ->pluck('home_team')
            ->unique();
            
        $tournaments = Game::select('tournament_name')
            ->orderBy('tournament_name')
            ->pluck('tournament_name')
            ->unique();
            
        // Ambil data pembelian tiket
        $purchasedQuantities = DB::table('orders')
            ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
            ->select('tickets.id', DB::raw('SUM(orders.quantity) as total_purchased'))
            ->groupBy('tickets.id')
            ->pluck('total_purchased', 'id');
            
        // Hanya tampilkan pertandingan yang belum selesai
        $query->where('match_time', '>=', Carbon::now())
              ->orderBy('match_time', 'asc');
            
        $games = $query->with('tickets')->paginate(10);
        
        return view('tickets.search', compact('games', 'teams', 'tournaments', 'purchasedQuantities'));
    }
}
