<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Carrega a página com a tabela
    public function index()
    {
        $categories = Category::all();
        return view('categories', compact('categories'));
    }

    // Salva uma nova categoria
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string'
        ]);

        Category::create($request->all());
        return redirect()->back(); // Volta para a tela atualizando a tabela
    }

    // Atualiza nome ou cor de uma categoria existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string'
        ]);

        Category::findOrFail($id)->update($request->all());
        return redirect()->back();
    }

    // Exclui a categoria
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect()->back();
    }
}