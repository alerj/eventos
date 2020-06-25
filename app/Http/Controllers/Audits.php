<?php

namespace App\Http\Controllers;

use App\Data\Models\Audit;
use App\Http\Controllers\Controller;

class Audits extends Controller
{
    public function index()
    {
        return $this->view('admin.audits.index')->with(
            'audits',
            Audit::orderBy('created_at', 'desc')->get()
        );
    }
}