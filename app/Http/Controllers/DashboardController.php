<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeedItem;

class DashboardController extends Controller
{
    public function index()
    {
        $feed = FeedItem::with('user')->latest()->get();
        return view('dashboard', compact('feed'));
    }
}
