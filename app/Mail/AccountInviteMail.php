<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $setupUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[MDRRMO Naic] You Have Been Invited — Set Up Your Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-invite',
            with: [
                'user'       => $this->user,
                'setupUrl'   => $this->setupUrl,
                'roleLabel'  => $this->user->roleLabel(),
                'privileges' => $this->user->rolePrivileges(),
                'loginEmail' => $this->user->email,
                'expiresAt'  => $this->user->invite_expires_at,
            ],
        );
    }
}