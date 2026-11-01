<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Store a new classification category in the Meridian Almanac.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('category', [
            'name' => ['required', 'string', 'max:40', 'unique:categories,name'],
            'code' => ['nullable', 'string', 'max:5'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'name.required' => 'Enter a category name to add a classification.',
            'name.unique' => 'That category name already exists in the rail.',
        ]);

        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $validated['code'] ?: substr($validated['name'], 0, 4)));
        if ($code === '') {
            $code = 'CATG';
        }

        $slug = Str::slug($validated['name']);
        if (Category::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(3));
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'code' => substr($code, 0, 5),
            'color' => $validated['color'],
        ]);

        return redirect()
            ->back()
            ->with('status', "Added classification [{$category->code}] {$category->name}.");
    }

    /**
     * Delete a custom classification category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('tasks.index')
            ->with('status', "Removed classification \"{$name}\"; its tasks remain in the general ledger.");
    }
}
