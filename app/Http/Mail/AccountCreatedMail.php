<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  User    $user              The newly created user
     * @param  string  $plainPassword     The plain-text password (sent once, then forgotten)
     */
    public function __construct(
        public readonly User   $user,
        public readonly string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[MDRRMO Naic] Your System Account Has Been Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-created',
            with: [
                'user'          => $this->user,
                'plainPassword' => $this->plainPassword,
                'loginUrl'      => route('login'),
                'privileges'    => $this->user->rolePrivileges(),
                'roleLabel'     => $this->user->roleLabel(),
            ],
        );
    }
}