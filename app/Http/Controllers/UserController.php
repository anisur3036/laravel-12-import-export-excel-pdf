<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\UserImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function index(Request $request): BinaryFileResponse
    {
        $date = $request->input('date', now());
        return Excel::download(new UserExport($date), 'users.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new UserImport, $request->file('file'));

            return back()->with('success', 'Users imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing users: ' . $e->getMessage());
        }
    }
}
