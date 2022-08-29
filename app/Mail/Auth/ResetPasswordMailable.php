<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('app@openbudget.com', 'Open Budget')
                    ->subject('Your password reset link')
                    ->markdown('emails.auth.reset', [
                        'name' => $this->name,
                        'url' => $this->url,
                    ]);
    }
}
