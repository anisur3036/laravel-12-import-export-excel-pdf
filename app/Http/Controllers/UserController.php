<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\UserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use TCPDF;
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


    public function exportPdf()
    {
        // Create new TCPDF instance
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Application');
        $pdf->SetTitle('Users List');

        // Set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Users List', 'Generated on ' . date('Y-m-d H:i:s'));

        // Set header and footer fonts
        $pdf->setHeaderFont(['helvetica', '', PDF_FONT_SIZE_MAIN]);
        $pdf->setFooterFont(['helvetica', '', PDF_FONT_SIZE_DATA]);

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 11);

        // Table header
        $header = ['Name', 'Email'];

        // Start table
        $html = '<table border="1" cellpadding="4">';

        // Add header row
        $html .= '<tr>';
        foreach ($header as $col) {
            $html .= '<th style="background-color: #CCCCCC;"><b>' . $col . '</b></th>';
        }
        $html .= '</tr>';

        // Process users in chunks
        User::select('name', 'email')
            ->orderBy('id')
            ->chunk(1000, function ($users) use (&$pdf, &$html) {
                foreach ($users as $user) {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($user->name) . '</td>';
                    $html .= '<td>' . htmlspecialchars($user->email) . '</td>';
                    $html .= '</tr>';
                }

                // Write the chunk to PDF to free up memory
                if (strlen($html) > 50000) { // Write to PDF if HTML gets too large
                    $html .= '</table>';
                    $pdf->writeHTML($html, true, false, true, false, '');
                    // Start a new table for the next chunk
                    $html = '<table border="1" cellpadding="4">';
                }
            });

        // Write any remaining HTML
        if (strlen($html) > 0) {
            $html .= '</table>';
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        // Close and output PDF document
        return $pdf->Output('users_list.pdf', 'D');
    }
}
