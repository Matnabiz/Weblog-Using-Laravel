<p>Hello,</p>
<p>A new post has been created by {{ $authorName }} ({{ $authorEmail }}).</p>
<p><strong>Title:</strong> {{ $postTitle }}</p>
<p>You can view the post at the following link:</p>
<p><a href="{{ $postLink }}">View Post</a></p>
<p>Best regards,</p>
<p>{{ config('app.name') }}</p>
