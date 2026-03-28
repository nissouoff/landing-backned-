<?php

namespace App\Http\Controllers;

use App\Models\Landing;
use App\Models\LandingView;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $landings = $request->user()->landings()->orderBy('updated_at', 'desc')->get();
        
        return response()->json([
            'landings' => $landings,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:cosmetic,fashion,food,tech,home,sport,jewelry,services',
        ]);

        $slug = Landing::generateSlug($validated['name']);

        $landing = $request->user()->landings()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'type' => $validated['type'],
            'content' => [
                'brandName' => $validated['name'],
                'heroTitle' => 'Bienvenue',
                'heroSubtitle' => 'Découvrez nos produits',
                'ctaButton' => 'Découvrir',
                'contactEmail' => 'contact@exemple.com',
                'footerText' => '© 2026 Ma Boutique',
            ],
            'products' => [],
        ]);

        return response()->json([
            'landing' => $landing,
            'message' => 'Landing page créée avec succès',
        ], 201);
    }

    public function show(Request $request, Landing $landing)
    {
        if ($landing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json(['landing' => $landing]);
    }

    public function update(Request $request, Landing $landing)
    {
        if ($landing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'content' => 'sometimes|array',
            'products' => 'sometimes|array',
            'is_published' => 'sometimes|boolean',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $landing->name) {
            $validated['slug'] = Landing::generateSlug($validated['name']);
        }

        $landing->update($validated);

        return response()->json([
            'landing' => $landing,
            'message' => 'Modifications sauvegardées',
        ]);
    }

    public function destroy(Request $request, Landing $landing)
    {
        if ($landing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $landing->reviews()->delete();
        $landing->views_records()->delete();
        $landing->delete();

        return response()->json([
            'message' => 'Landing page supprimée',
        ]);
    }

    public function publish(Request $request, Landing $landing)
    {
        if ($landing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $landing->update(['is_published' => true]);

        return response()->json([
            'landing' => $landing,
            'message' => 'Landing page publiée',
        ]);
    }

    public function unpublish(Request $request, Landing $landing)
    {
        if ($landing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $landing->update(['is_published' => false]);

        return response()->json([
            'landing' => $landing,
            'message' => 'Landing page dépubliée',
        ]);
    }

    public function publicShow(string $slug)
    {
        $landing = Landing::where('slug', $slug)->first();
        
        if (!$landing) {
            return response()->json(['message' => 'Page non trouvée'], 404);
        }

        if (!$landing->is_published) {
            return response()->json(['message' => 'Page hors ligne'], 403);
        }

        $reviews = $landing->reviews()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'landing' => $landing,
            'reviews' => $reviews,
        ]);
    }

    public function trackView(Request $request, string $slug)
    {
        $landing = Landing::where('slug', $slug)->first();
        
        if (!$landing || !$landing->is_published) {
            return response()->json(['message' => 'Page non disponible'], 404);
        }

        $ipAddress = $request->ip();
        
        $existingView = LandingView::where('landing_id', $landing->id)
            ->where('ip_address', $ipAddress)
            ->first();

        if (!$existingView) {
            DB::transaction(function () use ($landing, $ipAddress) {
                LandingView::create([
                    'landing_id' => $landing->id,
                    'ip_address' => $ipAddress,
                ]);
                
                $landing->increment('views');
            });
        }

        return response()->json(['message' => 'View tracked']);
    }

    public function storeReview(Request $request, string $slug)
    {
        $landing = Landing::where('slug', $slug)->first();
        
        if (!$landing || !$landing->is_published) {
            return response()->json(['message' => 'Page non disponible'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review = $landing->reviews()->create([
            'name' => $validated['name'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'review' => $review,
            'message' => 'Avis ajouté avec succès',
        ], 201);
    }
}
