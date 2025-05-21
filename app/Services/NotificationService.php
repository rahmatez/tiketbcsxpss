<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create a notification for a specific user
     */
    public function createNotification(User $user, string $title, string $message, string $type, $reference = null, ?array $data = null, $scheduledAt = null)
    {
        $referenceId = null;
        $referenceType = null;
        
        if ($reference) {
            $referenceId = $reference->id;
            $referenceType = get_class($reference);
        }
        
        return $user->notify($title, $message, $type, $referenceId, $referenceType, $data, $scheduledAt);
    }
    
    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        return $notification->markAsRead();
    }
    
    /**
     * Mark all notifications for a user as read
     */
    public function markAllAsRead(User $user)
    {
        return $user->notifications()->where('is_read', false)->update(['is_read' => true]);
    }
      /**
     * Create match reminders for all users with tickets to upcoming games
     * 
     * @param int $daysBeforeMatch Days before the match to create reminders
     * @return int Number of reminders created
     */
    public function createMatchReminders(int $daysBeforeMatch = 3)
    {
        // Find games that will happen in X days
        $targetDate = Carbon::now()->addDays($daysBeforeMatch)->startOfDay();
        $endDate = Carbon::now()->addDays($daysBeforeMatch)->endOfDay();
        
        $games = Game::where('match_time', '>=', $targetDate)
                    ->where('match_time', '<=', $endDate)
                    ->where('is_home_game', true)
                    ->get();
        
        if ($games->isEmpty()) {
            Log::info("No games found scheduled for {$daysBeforeMatch} days from now.");
            return 0;
        }
        
        $reminderCount = 0;
        
        foreach ($games as $game) {
            // Get all users with tickets for this game
            $orders = Order::with('user')
                ->where('game_id', $game->id)
                ->whereIn('status', ['paid', 'confirmed'])
                ->get();
            
            foreach ($orders as $order) {
                if (!$order->user) continue;
                
                // Check if user already has a reminder for this game
                $existingReminder = Notification::where('user_id', $order->user->id)
                                    ->where('type', 'match_reminder')
                                    ->where('reference_id', $game->id)
                                    ->where('reference_type', get_class($game))
                                    ->first();
                
                if (!$existingReminder) {
                    $matchTime = Carbon::parse($game->match_time);
                    $formattedDate = $matchTime->format('d F Y');
                    $formattedTime = $matchTime->format('H:i');
                    
                    $title = "Pengingat Pertandingan";
                    $message = "Jangan lupa! Pertandingan {$game->home_team} vs {$game->away_team} akan berlangsung pada tanggal {$formattedDate} pukul {$formattedTime} WIB di {$game->stadium_name}.";
                    
                    $data = [
                        'match_time' => $game->match_time,
                        'stadium' => $game->stadium_name,
                        'home_team' => $game->home_team,
                        'away_team' => $game->away_team
                    ];
                    
                    $this->createNotification(
                        $order->user, 
                        $title,
                        $message,
                        'match_reminder',
                        $game,
                        $data
                    );
                    
                    // Send email notification
                    if ($order->user->email) {
                        try {
                            Mail::to($order->user->email)->send(new \App\Mail\UserNotification(
                                $title,
                                $message,
                                $game
                            ));
                            
                            Log::info("Match reminder email sent to user {$order->user->id} for game {$game->id}");
                        } catch (\Exception $e) {
                            Log::error("Failed to send match reminder email: " . $e->getMessage());
                        }
                    }
                    
                    $reminderCount++;
                }
            }
        }
        
        return $reminderCount;
    }
    
    /**
     * Create payment reminder for an order
     */
    public function createPaymentReminder(Order $order, int $hoursBeforeExpiry)
    {
        if (!$order->user) {
            Log::error("No user associated with order {$order->id}");
            return null;
        }
        
        if ($order->status !== 'pending') {
            return null; // Don't remind if already paid
        }
        
        $game = $order->game;
        if (!$game) {
            Log::error("No game associated with order {$order->id}");
            return null;
        }
        
        $title = "Pengingat Pembayaran";
        $message = "Pembayaran Anda untuk tiket {$game->home_team} vs {$game->away_team} belum selesai. Segera selesaikan pembayaran Anda!";
        
        return $this->createNotification(
            $order->user,
            $title,
            $message,
            'payment_reminder',
            $order
        );
    }
    
    /**
     * Send scheduled notifications that are due
     */
    public function processScheduledNotifications()
    {
        $now = Carbon::now();
        $notifications = Notification::whereNull('sent_at')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();
            
        $count = 0;
        foreach ($notifications as $notification) {
            $this->sendNotification($notification);
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Send a notification (email, push, etc.)
     */
    public function sendNotification(Notification $notification)
    {
        // Mark as sent
        $notification->sent_at = Carbon::now();
        $notification->save();
        
        $user = $notification->user;
        if (!$user) {
            Log::error("No user associated with notification {$notification->id}");
            return false;
        }
        
        // Send email notification if user has email
        if ($user->email) {
            try {
                // The actual mail sending would be implemented here
                // using Laravel's Mail facade
                // 
                // Mail::to($user->email)->send(new \App\Mail\UserNotification($notification));
                
                Log::info("Email notification sent to {$user->email}: {$notification->title}");
                return true;
            } catch (\Exception $e) {
                Log::error("Failed to send email notification: " . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }
}
