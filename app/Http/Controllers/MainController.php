<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Models\Category;
use App\Services\Operations;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // Importação necessária para a verificação de senha

class MainController extends Controller
{
    public function index(Request $request)
    {
        // get search input
        $search = $request->input('search');

        // load user's notes
        $id = session('user.id');
        
        // base query
        $query = User::find($id)
                    ->notes()
                    ->with('categories')
                    ->whereNull('deleted_at');

        // apply search filter if exists
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('text', 'like', "%{$search}%");
            });
        }

        // order by pinned first, then position, then newest
        $query->orderBy('is_pinned', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('created_at', 'desc');

        // get results
        $notes = $query->get()->toArray();

        // show home view
        return view('home', ['notes' => $notes, 'search' => $search]);
    }

    public function newNote()
    {
        $categories = Category::all();
        return view('new_note', ['categories' => $categories]);
    }

    public function newNoteSubmit(Request $request)
    {
        // validate request
        $request->validate(
            // rules
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            // error messages
            [
                'text_title.required' => 'O título é obrigatório',
                'text_title.min' => 'O título deve ter pelo menos :min caracteres',
                'text_title.max' => 'O título deve ter no máximo :max caracteres',
                'text_note.required' => 'A nota é obrigatória',
                'text_note.min' => 'A nota deve ter pelo menos :min caracteres',
                'text_note.max' => 'A nota deve ter no máximo :max caracteres'
            ]
        );

        // get user id
        $id = session('user.id');

        // create new note
        $note = new Note();
        $note->user_id = $id;
        $note->title = $request->text_title;
        $note->text = $request->text_note;// Aplicação do HTMLPurifier
        $note->is_protected = $request->has('is_protected'); // <-- Salvando a Proteção
        $note->save();

        // attach categories
        if ($request->has('categories')) {
            $note->categories()->attach($request->categories);
        }

        // redirect to home
        return redirect()->route('home');
    }

    public function editNote($id)
    {
        $id = Operations::decryptId($id);

        if($id === null){
            return redirect()->route('home');
        }
        
        // load note with categories
        $note = Note::with('categories')->find($id);
        
        // load categories
        $categories = Category::all();

        // show edit note view
        return view('edit_note', ['note' => $note, 'categories' => $categories]);
    }

    public function editNoteSubmit(Request $request)
    {
        // validate request
        $request->validate(
            // rules
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            // error messages
            [
                'text_title.required' => 'O título é obrigatório',
                'text_title.min' => 'O título deve ter pelo menos :min caracteres',
                'text_title.max' => 'O título deve ter no máximo :max caracteres',
                'text_note.required' => 'A nota é obrigatória',
                'text_note.min' => 'A nota deve ter pelo menos :min caracteres',
                'text_note.max' => 'A nota deve ter no máximo :max caracteres'
            ]
        );

        // check if note_id exists
        if($request->note_id == null){
            return redirect()->route('home');
        }

        // decrypt note_id
        $id = Operations::decryptId($request->note_id);

        if($id === null){
            return redirect()->route('home');
        }

        // load note
        $note = Note::find($id);

        // update note
        $note->title = $request->text_title;
        $note->text = $request->text_note; // Aplicação do HTMLPurifier
        $note->is_protected = $request->has('is_protected'); // <-- Atualizando a Proteção
        $note->save();

        // sync or detach categories
        if ($request->has('categories')) {
            $note->categories()->sync($request->categories);
        } else {
            $note->categories()->detach();
        }

        // redirect to home
        return redirect()->route('home');
    }

    public function deleteNote($id)
    {
        $id = Operations::decryptId($id);

        if($id === null){
            return redirect()->route('home');
        }
        
        // load note
        $note = Note::find($id);

        // show delete note confirmation
        return view('delete_note', ['note' => $note]);
    }

    public function deleteNoteConfirm($id)
    {
        // check if $id is encrypted
        $id = Operations::decryptId($id);

        if($id === null){
            return redirect()->route('home');
        }

        // load note
        $note = Note::find($id);

        // 1. hard delete
        // $note->delete();

        // 2. soft delete
        // $note->deleted_at = date('Y:m:d H:i:s');
        // $note->save();

        // 3. soft delete (property SoftDeletes in model)
        $note->delete();

        // 4. hard delete (property SoftDeletes in model)
        // $note->forceDelete();

        // redirect to home
        return redirect()->route('home');
    }

    public function pinNote($id)
    {
        $id = Operations::decryptId($id);

        if($id === null){
            return redirect()->route('home');
        }
        
        $note = Note::find($id);
        $note->is_pinned = !$note->is_pinned; // Inverte o status (true vira false e vice-versa)
        $note->save();

        return redirect()->route('home');
    }

    public function trash()
    {
        $id = session('user.id');
        // Busca apenas as notas que possuem deleted_at preenchido
        $notes = Note::onlyTrashed()
                    ->with('categories')
                    ->where('user_id', $id)
                    ->orderBy('deleted_at', 'desc')
                    ->get()
                    ->toArray();

        return view('trash', ['notes' => $notes]);
    }

    public function restoreNote($id)
    {
        $id = Operations::decryptId($id);
        if($id === null){ return redirect()->route('trash'); }
        
        $note = Note::onlyTrashed()->find($id);
        if($note) { $note->restore(); } // Restaura limpando o deleted_at
        
        return redirect()->route('trash');
    }

    public function forceDeleteNote($id)
    {
        $id = Operations::decryptId($id);
        if($id === null){ return redirect()->route('trash'); }
        
        $note = Note::onlyTrashed()->find($id);
        if($note) { $note->forceDelete(); } // Exclui definitivamente do banco
        
        return redirect()->route('trash');
    }

    public function publicNote($public_id){
    
        $note = Note::with(['categories', 'user'])->where('public_id', $public_id)->firstOrFail();    
        
        if ($note->expires_at && $note->expires_at->isPast()) {
            abort(404, 'Este link expirou e não está mais disponível.');
        }
        
        return view('public_note', ['note' => $note]);
    }

    public function exportPdf($id)
    {
        $id = Operations::decryptId($id);
        if($id === null){ return redirect()->route('home'); }
        
        $note = Note::with(['categories', 'user'])->find($id);
        
        // Passa a variável $is_pdf como verdadeira para o PDF
        $pdf = Pdf::loadView('public_note', [
            'note' => $note,
            'is_pdf' => true
        ]);
        
        return $pdf->download(Str::slug($note->title) . '.pdf');
    }

    public function uploadImage(Request $request)
    {
        // Valida se enviou uma imagem
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Cria um nome único para o arquivo
            $filename = time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            
            // Salva fisicamente no S3 dentro da pasta 'notas'
            $path = $file->storeAs('notas', $filename, 's3');
            
            // Recupera a URL pública definitiva da AWS
            $url = Storage::disk('s3')->url($path);

            // Devolve a URL em JSON para o JavaScript
            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'Falha no upload'], 400);
    }

    public function autosave(Request $request)
    {
        // Validação básica
        $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string',
            'text_note' => 'required|string'
        ]);

        // Busca a nota garantindo que pertence ao usuário logado
        $note = Note::where('id', $request->id)
                    ->where('user_id', session('user.id')) // Ajuste para a forma como você controla a sessão
                    ->first();

        if ($note) {
            $note->title = $request->title;
            $note->text_note = $request->text_note; // Aplicação do HTMLPurifier
            $note->save();

            return response()->json([
                'status' => 'success', 
                'time' => now()->format('H:i:s')
            ]);
        }

        return response()->json(['status' => 'error'], 400);
    }

    public function setExpiration(Request $request)
    {
        $note = Note::where('id', $request->note_id)
                    ->where('user_id', session('user.id'))
                    ->first();
        
        if ($note) {
            if ($request->expires_in == '24h') {
                $note->expires_at = now()->addHours(24);
            } elseif ($request->expires_in == '7d') {
                $note->expires_at = now()->addDays(7);
            } else {
                $note->expires_at = null; // Sem validade (permanente)
            }
            
            $note->save();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array'
        ]);

        foreach ($request->order as $index => $noteId) {
            // Usa o índice do array como a nova posição numérica
            Note::where('id', $noteId)
                ->where('user_id', session('user.id')) // Ajuste conforme sua sessão
                ->update(['position' => $index]);
        }

        return response()->json(['status' => 'success']);
    }

    public function toggleChecklist(Request $request)
    {
        $note = Note::where('id', $request->note_id)
                    ->where('user_id', session('user.id')) // Proteção do usuário
                    ->first();
        
        if ($note) {
            $note->text = $request->text_note; // Aplicação do HTMLPurifier
            $note->save();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    public function unlockNote(Request $request)
    {
        $request->validate([
            'note_id' => 'required|integer',
            'password' => 'required|string'
        ]);

        $user = User::find(session('user.id'));

        // Verifica a senha do usuário
        if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            $note = Note::where('id', $request->note_id)->where('user_id', $user->id)->first();
            
            if ($note) {
                // Não salvamos mais na sessão! Desbloqueio vivo apenas no navegador do usuário.
                return response()->json(['status' => 'success', 'text' => $note->text]);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Senha incorreta.'], 401);
    }

    public function setReminder(Request $request)
    {
        $request->validate([
            'note_id' => 'required|integer',
            'reminder_at' => 'nullable|date'
        ]);

        $note = Note::where('id', $request->note_id)
                    ->where('user_id', session('user.id'))
                    ->first();
        
        if ($note) {
            $note->reminder_at = $request->reminder_at;
            $note->save();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }
}