<?php

namespace App\Http\Controllers;

use App\Mail\Reported;
use App\Rules\HasReportTarget;
use App\Models\ReportCategory;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $params = [
            'id' => $request->target === 'posts' ? $request->post : $request->comment_id,
            'target' => $request->target,
            'report_categories' => ReportCategory::get(),
            'user' => $user,
        ];

        return view('report.create', $params);
    }

    public function store(Request $request)
    {
        $validate_data = $request->validate([
            'id' => ['required', new HasReportTarget()],
            'target' => ['required'],
            'report_category_id' => ['required', 'exists:report_categories,id'],
            'comment' => ['required_if:report_category_id,999']
        ]);

        $params = [
            'target_id' => $request->id,
            'category' => $request->report_category_id,
            'table_name' => $request->target,
            'comment' => $request->comment,
        ];

        Mail::send(new Reported($validate_data));
        Report::create($params);

        return view('report.reported');
    }
}
