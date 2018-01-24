<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{

    public function index(int $page = 0, int $perPage = 10)
    {
        
        // Берем список сообщений, которые не ответы (не имеют parent), и присоединяем к ним ответы если есть
        // Деревянная структура, 1 уровень - сообщения, 2 - ответы (чтобы не плодить сущности которые по сути одинаковые)
        $query = DB::table('messages AS m')
            ->join('users AS u', 'u.id', '=', 'm.user_id')
            ->leftJoin('messages AS ma', 'ma.parent_id', '=', 'm.id')
            ->select('m.*', 'u.name AS user_name', 'ma.message AS answer', 'ma.created_at AS answer_created_at')
            ->whereNull('m.parent_id')
            ->orderBy('created_at', 'desc')
        ;

        if ($page && $perPage) {
            $query = $query
                ->skip($perPage * ($page - 1))
                ->take($perPage)
            ;
        }

        return $query->get();

//        return $page && $perPage
//            ? Message::latest()->offset($perPage * ($page - 1))->limit($perPage)->get():
//            Message::all()
//        ;
    }
 
    public function count()
    {
        return Message::count();
    }
 
    public function show(Message $message)
    {
        return $message;
    }

    public function message(Request $request)
    {
        
        $user = Auth::guard('api')->user();

        $message = new Message();
        $message->user_id = $user->id;
        $message->message = $request->input('message');
        $message->save();

        event(new \App\Events\NewMessage($user, $message));
            
        return response()->json($message, 201);
    }

    public function answer(Request $request, Message $message)
    {
        
        $user = Auth::guard('api')->user();

        if (!$user->is_admin || $message->parent_id) {
            return $this->noFound($request);
        }

        // Ищем ответ, если нету создаем новый, иначе перезаписываем старый
        $answer = Message::where('parent_id', $message->id)->first();
        if (!$answer) {
            $answer = new Message();
            $answer->parent_id = $message->id;
        }

        $answer->user_id = $user->id;
        $answer->message = $request->input('message');
        $answer->save();

        event(new \App\Events\NewAnswer(\App\User::find($message->user_id), $message, $answer));
            
        return response()->json($answer, 201);
    }

    public function noFound(Request $request)
    {
        return response()->json([
            'data' => 'Resource not found'
        ], 404);
    }

}
