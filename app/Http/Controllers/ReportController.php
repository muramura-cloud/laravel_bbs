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
            'post_id' => $request->post,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from
        ];

        return view('report.create', $params);
    }

    public function store(Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

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

        $params = [
            'user' => $user,
            'post_id' => $request->post_id,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from
        ];

        return redirect()->route('report_reported', $params);
    }

    public function showReported(Request $request)
    {
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $params = [
            'user' => $user,
            'post_id' => $request->post_id,
            'page' => $request->page,
            'keyword' => $request->keyword,
            'category' => $request->category,
            'do_name_search' => $request->do_name_search,
            'from' => $request->from
        ];

        return view('report.reported', $params);
    }
}
