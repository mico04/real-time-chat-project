<?php

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard', [
//         'users' => User::whereNot('id', auth()->id())->get()
//     ]);
// })->middleware(['auth'])->name('dashboard');

// Route::get('/chat/{friend}', function (User $friend) {
//     return view('chat', [
//         'friend' => $friend
//     ]);
// })->middleware(['auth'])->name('chat');

Route::get('/chat', function () {
    return view('chat', [
        'friend' => 'Real-time Chat',
        'current_user' => json_encode(auth()->user()->getAuthIdentifier())
    ]);
})
->middleware(['username.auth', 'no.cache'])
->name('chat');

// Route::get('/messages/{friend}', function (User $friend) {
//     return ChatMessage::query()
//         ->where(function ($query) use ($friend) {
//             $query->where('sender_id', auth()->id())
//                 ->where('receiver_id', $friend->id);
//         })
//         ->orWhere(function ($query) use ($friend) {
//             $query->where('sender_id', $friend->id)
//                 ->where('receiver_id', auth()->id());
//         })
//        ->with(['sender', 'receiver'])
//        ->orderBy('id', 'asc')
//        ->get();
// })->middleware(['auth']);

Route::get('/messages', function (User $friend) {
    return Cache::get('messages', []);
});

Route::post('/messages/{friend}', function (User $friend) {
    $message = ChatMessage::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $friend->id,
        'text' => request()->input('message')
    ]);

    broadcast(new MessageSent($message));

    return  $message;
});

Route::post('/send-message', function () {
    // $message = ChatMessage::create([
    //     'sender_id' => auth()->id(),
    //     'receiver_id' => $friend->id,
    //     'text' => request()->input('message')
    // ]);

    $messages = Cache::get('messages', []);

    $message = [
        'sender_id' => auth()->user()->getAuthIdentifier(),
        'text' => request()->input('message')
    ];

    $messages[] = $message;

    Cache::put('messages', $messages, now()->endOfDay());

    broadcast(new MessageSent($message));

    return $message;
});

require __DIR__ . '/auth.php';
