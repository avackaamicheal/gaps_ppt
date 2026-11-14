<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $currentAction = $request->query('action');
        $search = $request->query('search');

        $query = ActivityLog::with('user:id,name,email');

        if ($currentAction) {
            $query->where('action', $currentAction);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->recent()->paginate(25)->withQueryString();

        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.activity_logs.index', compact('logs', 'actions', 'currentAction', 'search'));
    }
}

