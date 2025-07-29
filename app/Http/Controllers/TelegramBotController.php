<?php


namespace App\Http\Controllers;

use App\Services\MFSService;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    protected string $botToken;
    protected $mfsService;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->mfsService = new MFSService();
    }

    public function webhook(Request $request)
    {
        $data = $request->all();

        // Handle both normal messages and button clicks (callback queries)
        $chat_id = $data['message']['chat']['id'] ?? $data['callback_query']['message']['chat']['id'] ?? null;
        $text = $data['message']['text'] ?? $data['callback_query']['data'] ?? null;

        if (!$chat_id || !$text) {
            return response()->json(['status' => 'Missing chat_id or text']);
        }

        if (isset($data['callback_query'])) {
            $callback_id = $data['callback_query']['id'];
            $this->answerCallbackQuery($callback_id);
        }

        // Step 1: Start command
        if ($text == '/start' || $text == 'menu') {
            $this->indexMessage($chat_id);
        } elseif ($text == 'mfs_cashout_charge') {
            $this->sendMessage($chat_id, $this->mfsService->index());
        } elseif (is_numeric($text)) {

            $this->sendMessage($chat_id, $this->mfsService->chargeCalculator($text), [
                'inline_keyboard' => [
                    [
                        ['text' => 'MENU', 'callback_data' => 'menu']
                    ]
                ]
            ]);
        } else {
            $this->indexMessage($chat_id);
        }

        return response('ok', 200);
    }

    protected function sendMessage($chat_id, $text, $reply_markup = null)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $payload = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($reply_markup) {
            $payload['reply_markup'] = json_encode($reply_markup);
        }

        file_get_contents($url . '?' . http_build_query($payload));
    }

    protected function answerCallbackQuery($callback_id)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery";
        $payload = ['callback_query_id' => $callback_id];

        file_get_contents($url . '?' . http_build_query($payload));
    }

    public function indexMessage($chat_id)
    {
        $this->sendMessage($chat_id, "👋 Welcome! Select a service:", [
            'inline_keyboard' => [
                [
                    ['text' => '📱 MFS Charge Calculator', 'callback_data' => 'mfs_cashout_charge'],
                    ['text' => '🌤️ Weather', 'callback_data' => 'weather']
                ],
            ]
        ]);

    }
}
