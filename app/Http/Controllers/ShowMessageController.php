<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShowMessageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $table = $request->table;
            $column_message = $request->column;
            $data = DB::table($table)
                ->where('id', $request->id)
                ->first();
            $type = $data->type;
            // if not exists $data->keyword, fill keyword with name table
            $keyword = $data->keyword ?? 'Preview ' . $table;
            $message = ($data->$column_message);

            switch ($type) {
                case 'text':
                    $msg = [
                        'keyword' => $keyword,
                        'text' => json_decode($message)->text,
                    ];
                    break;
                case 'media':
                    $msg = [
                        'keyword' => $keyword,
                        'message' => json_decode($message),

                        // 'image' => json_decode($message)->image
                        //     ->url,
                    ];
                    break;
                case 'button':
                    $msg = [
                        'keyword' => $keyword,
                        'message' =>
                        json_decode($message)->text ??
                            json_decode($message)->caption,
                        'footer' => json_decode($message)->footer,
                        'buttons' => json_decode($message)
                            ->buttons,
                        'image' =>
                        json_decode($message)->image->url ??
                            null,
                    ];
                    break;
                case 'template':
                    $msg = [
                        'keyword' => $keyword,
                        'message' =>
                        json_decode($message)->text ??
                            json_decode($message)->caption,
                        'footer' => json_decode($message)->footer,
                        'templates' => json_decode($message)
                            ->templateButtons,
                        'image' =>
                        json_decode($message)->image->url ??
                            null,
                    ];
                    break;
                default:
                    return view('ajax.messages.emptyshow')->render();
                    break;
            }
            return view(
                'ajax.messages.' . $type . 'show',
                $msg
            )->render();
        } catch (\Throwable $th) {
            Log::error($th);
            return view('ajax.messages.emptyshow')->render();
        }
    }

    public function getFormByType($type, Request $request)
    {
        if ($request->ajax()) {
            return view('ajax.messages.form' . $type)->render();
        }
        return 'http request';
    }
}
