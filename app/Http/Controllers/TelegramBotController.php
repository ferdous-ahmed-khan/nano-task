<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function webhook(Request $request)
    {
        $data = $request->all();
        $chat_id = $data['message']['chat']['id'];
        $text = $data['message']['text'] ?? '';

        if (str_starts_with($text, '/add')) {
            $taskText = trim(str_replace('/add', '', $text));
            Task::create([
                'user_id' => $chat_id,
                'task' => $taskText
            ]);
            $this->sendMessage($chat_id, "Added-> $taskText");
        } elseif ($text === '/list') {
            $tasks = Task::where('user_id', $chat_id)->get();
            $reply = "Your tasks:\n";
            foreach ($tasks as $index => $task) {
                $reply .= ($index + 1) . '. ' . $task->task . "\n";
            }
            $this->sendMessage($chat_id, $reply ?: "No tasks found.");
        } else {
            $this->sendMessage($chat_id, "Commands:\n/add TaskName\n/list");
        }

        return response()->json(['status' => 'ok']);
    }

    private function sendMessage($chat_id, $text)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $text
        ]);
    }
}
