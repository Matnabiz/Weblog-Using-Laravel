<?php

namespace App\Mail;

use App\Models\Post;
use Resources\Views\PostNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $author;

    public function __construct(Post $post, $author)
    {
        $this->post = $post;
        $this->author = $author;
    }

    public function build()
    {
        $postLink = url('/posts/' . $this->post->id);

        return $this->subject('New Post Notification')
                    ->view('emails.postNotification')
                    ->with([
                        'authorName' => $this->author->name,
                        'authorEmail' => $this->author->email,
                        'postTitle' => $this->post->title,
                        'postLink' => $postLink,
                    ]);
    }
}
