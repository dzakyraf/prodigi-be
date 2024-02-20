<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\NewProposalEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Laravel\Facades\Telegram;


class BotTelegramController extends Controller
{
    public function setWebhook()
    {

        Mail::to("approver1@gmail.com")->send(new NewProposalEmail([
            'status' => 'info',
            'procurement_title' => 'Procurement',
            'nodin' => 'NO 01/01::100',
            'title' => 'Ada Pengajuan Butuh Persetjuan Anda',
            'messages' => 'Cek Aplikasi Prodigi Untuk Melihat Detail',
        ]));
         Telegram::sendMessage([
            'chat_id' => '94867628',
            'text' => 'Pengajuan Anda Telah Disetujui, sedang diteruskan untuk Approval Selanjutanya '.PHP_EOL.' Pengajuan : Pengajuan 1'.PHP_EOL.' Nodin : NO/01/01'
        ]);

        // $response = Telegram::setWebhook(['url' => 'https://6a9f-2a09-bac5-3a24-1028-00-19c-2d.ngrok.io/api/prodigibot/webhook']);
        // dd($response);
    }


    public function commandHandlerWebHook()
    {



        $updates = Telegram::commandsHandler(true);

        // Log::build([
        //     'driver' => 'single',
        //     'path' => storage_path('logs/custom.log'),
        // ])->info(json_encode($updates->getChat()->getId()));

        $chat_id = $updates->getChat()->getId();
        $username = $updates->getChat()->getFirstName();


        DB::table('telegram_users')->updateOrInsert([
            "username" => $username,
            "first_name" => "",
            "chat_id" => $chat_id,
        ], ["chat_id" => $chat_id]);



        return Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'User berhasil didaftarkan !' . $username
        ]);


    }
}
