<?php

namespace App\Http\Controllers;

use App\Models\FeedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('feed_images', 'public');
        }

        FeedItem::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'image_path' => $path,
            'type' => 'post',
        ]);

        return redirect()->back();
    }

    public function like(FeedItem $feed)
    {
        $feed->increment('likes');
        return back();
    }

    public function destroy(FeedItem $feed)
{
    if ($feed->user_id !== auth()->id()) {
        abort(403);
    }

    if ($feed->image_path) {
        Storage::disk('public')->delete($feed->image_path);
    }

    $feed->delete();

    return redirect()->back()->with('success', 'Postarea a fost ștearsă.');
}
}
