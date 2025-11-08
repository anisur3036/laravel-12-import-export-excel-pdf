<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function index(): BinaryFileResponse
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }
}
