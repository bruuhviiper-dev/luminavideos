<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MonetizationController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Simulação de queries para o Dashboard
        $earnings = DB::table('creator_earnings')
            ->where('user_id', $user->id)
            ->sum('amount');
            
        $members = DB::table('channel_memberships')
            ->where('channel_id', $user->id)
            ->where('status', 'active')
            ->count();
            
        $recentTransactions = DB::table('creator_earnings')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
            
        $plan = DB::table('membership_plans')
            ->where('user_id', $user->id)
            ->first();

        return view('studio.monetization.dashboard', compact('user', 'earnings', 'members', 'recentTransactions', 'plan'));
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'price' => 'required|numeric|min:1.99|max:500',
            'name' => 'required|string|max:50'
        ]);

        $user = Auth::user();
        
        DB::table('membership_plans')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'name' => $request->name,
                'price' => $request->price,
                'is_active' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('status', 'Clube de Membros configurado e ativado com sucesso!');
    }

    public function subscribe(Request $request, $channelUsername)
    {
        $channel = User::where('username', $channelUsername)->firstOrFail();
        $plan = DB::table('membership_plans')->where('user_id', $channel->id)->where('is_active', true)->first();
        
        if (!$plan) {
            return back()->withErrors(['error' => 'Canal não possui clube de membros.']);
        }

        // SIMULAÇÃO DO GATEWAY DE PAGAMENTO (Falso Checkout Dinâmico)
        DB::table('channel_memberships')->insert([
            'subscriber_id' => Auth::id(),
            'channel_id' => $channel->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
            'gateway_subscription_id' => 'sim_' . uniqid(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Registrar ganho simulado para o criador
        DB::table('creator_earnings')->insert([
            'user_id' => $channel->id,
            'amount' => $plan->price * 0.70, // 30% de taxa da plataforma
            'type' => 'subscription',
            'description' => 'Assinatura de ' . Auth::user()->name,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('channel.show', $channel->username)
            ->with('status', 'Você agora é membro de ' . $channel->name . '!');
    }

    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50',
            'pix_key' => 'required|string|max:100'
        ]);

        $user = Auth::user();
        $earnings = DB::table('creator_earnings')->where('user_id', $user->id)->sum('amount');
        $withdrawn = DB::table('withdrawal_requests')->where('user_id', $user->id)->whereIn('status', ['paid', 'pending'])->sum('amount');
        
        $available = $earnings - $withdrawn;

        if ($request->amount > $available) {
            return back()->withErrors(['amount' => 'Saldo insuficiente. Saldo disponível: R$ ' . number_format($available, 2, ',', '.')]);
        }

        DB::table('withdrawal_requests')->insert([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'pix_key' => $request->pix_key,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('status', 'Saque solicitado com sucesso! O pagamento será via Pix em até 2 dias úteis.');
    }
}
