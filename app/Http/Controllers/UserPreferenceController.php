<?php

namespace App\Http\Controllers;

use App\Repositories\UserPreferenceRepository;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    protected $repo;

    public function __construct(UserPreferenceRepository $repo)
    {
        $this->repo = $repo;
    }

    // Save or update user preferences
    public function store(Request $request)
    {
        $request->validate([
            'sources' => 'array',
            'categories' => 'array',
            'authors' => 'array',
        ]);

        $preferences = $this->repo->savePreferences(
            auth()->id(),
            $request->input('sources', []),
            $request->input('categories', []),
            $request->input('authors', [])
        );

        return response()->json([
            'message' => 'Preferences saved successfully.',
            'preferences' => $preferences
        ]);
    }

    // Fetch user preferences
    public function show()
    {
        $preferences = $this->repo->getPreferences(auth()->id());

        return response()->json($preferences);
    }
}
