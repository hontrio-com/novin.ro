<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccountService
{
    /**
     * Creeaza cont automat pentru client dupa finalizarea auditului.
     * Daca exista deja cont cu acelasi email, leaga auditul de contul existent.
     */
    public function createOrAttach(Audit $audit): User
    {
        $existingUser = User::where('email', $audit->email)->first();

        if ($existingUser) {
            // Leaga auditul de contul existent
            $audit->update(['user_id' => $existingUser->id]);
            return $existingUser;
        }

        // Genereaza parola temporara usor de citit
        $password = $this->generateReadablePassword();

        $user = User::create([
            'name'              => $this->extractName($audit->email),
            'email'             => $audit->email,
            'password'          => Hash::make($password),
            'email_verified_at' => now(), // verificat implicit prin plata
        ]);

        // Leaga auditul
        $audit->update(['user_id' => $user->id]);

        // Trimite email cu credentialele
        $this->sendWelcomeEmail($user, $password, $audit);

        return $user;
    }

    private function generateReadablePassword(): string
    {
        // Parola de forma: Cuvant123! - usor de memorat
        $words = ['Audit', 'Inovex', 'Report', 'Score', 'Check', 'Speed', 'Cloud', 'Smart'];
        $word  = $words[array_rand($words)];
        $num   = rand(100, 999);
        $chars = ['!', '@', '#', '$'];
        $char  = $chars[array_rand($chars)];
        return $word . $num . $char;
    }

    private function extractName(string $email): string
    {
        $local = explode('@', $email)[0];
        $local = str_replace(['.', '_', '-'], ' ', $local);
        return ucwords($local);
    }

    private function sendWelcomeEmail(User $user, string $password, Audit $audit): void
    {
        try {
            Mail::send('emails.welcome_account', [
                'user'     => $user,
                'password' => $password,
                'audit'    => $audit,
            ], function ($m) use ($user) {
                $m->to($user->email)
                  ->subject('Contul tau Inovex Audit a fost creat');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AccountService: Failed to send welcome email: ' . $e->getMessage());
        }
    }
}