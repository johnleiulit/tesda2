<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentBatch;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class AttendancePdfController extends Controller
{
    public function generate(AssessmentBatch $assessment_batch)
    {
        $templatePath = storage_path('app/public/forms/assessment_attendance.pdf');
        if (!file_exists($templatePath)) {
            abort(404, 'Attendance template not found: ' . $templatePath);
        }

        // Create FPDI - ONLY ONE PAGE
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->setSourceFile($templatePath);

        // Add ONLY page 1
        $pdf->AddPage();
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx, 0, 0, 210);

        // Mapping array - CORRECTED COORDINATES
        $mapping = [
            'nc_program' => [
                'x' => 85,  // Changed from 363
                'y' => 95,   // Changed from 312
                'font' => 'Arial',
                'size' => 10.5,
                'style' => '',
            ],
            'training_center' => [
                    'value' => 'SACRED HEART COLLEGE OF LUCENA CITY, INC',
                    'x' => 70,
                    'y' => 106.5, 
                    'font' => 'Arial',
                    'size' => 10,
                    'style' => '',

            ],
            'assessment_date' => [
                'x' => 70,  // Changed from 363
                'y' => 117.5,   // Changed from 345
                'font' => 'Arial',
                'size' => 11,
                'style' => '',
            ],
            'table_start_x' => 21,      // Changed from 363
            'table_start_y' => 120,      // Changed from 366
            'table_row_height' => 5,
            'table_col_widths' => [50, 0, 40, 35, 35],
        ];

        // Fill in batch info
        $this->writeTextIfExists($pdf, $mapping['nc_program'], $assessment_batch->nc_program, true);
        $this->writeTextIfExists($pdf, $mapping['assessment_date'], $assessment_batch->assessment_date->format('M d, Y'));
        $this->writeTextIfExists($pdf, $mapping['training_center'], $mapping['training_center']['value']);

        // Get ONLY first 10 applicants
        $applicants = $assessment_batch->applications()->take(10)->get();

       
        $rowY = [131,136.5,142,147.5,153,158.5,164,169.5,175,180.5];

        foreach ($applicants as $i => $applicant) {

            $y = $rowY[$i];

            $pdf->SetFont('Arial','',9);
            $pdf->SetXY(22,$y);
            $pdf->Write(4,$this->formatFullName($applicant));

            $pdf->SetXY(76.5,$y);
            $pdf->Write(4,$applicant->reference_number ?? '');
        }

                // Output - ONLY ONE PAGE
                $content = $pdf->Output('', 'S');
                return response($content, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="attendance_'.$assessment_batch->id.'.pdf"');
            }

    private function formatFullName($applicant)
    {
        $lastName = $applicant->surname ?? '';
        $firstName = $applicant->firstname ?? '';
        $middleInitial = $applicant->middleinitial ?? '';

        return trim($lastName . ', ' . $firstName . ' ' . $middleInitial);
    }

    private function writeTextIfExists(Fpdi $pdf, $meta, $value, $center = false)
    {
        if (empty($meta) || $value === null || $value === '') return;

        $font = $meta['font'] ?? 'Arial';
        $size = $meta['size'] ?? 9;
        $style = $meta['style'] ?? '';
        $x = $meta['x'] ?? 0;
        $y = $meta['y'] ?? 0;

        $pdf->SetFont($font, $style, $size);

        if ($center) {
            $pageWidth = $pdf->GetPageWidth();
            $textWidth = $pdf->GetStringWidth((string)$value);
            $x = ($pageWidth - $textWidth) / 2;
        }

        $pdf->SetXY($x, $y);
        $pdf->Write(4, (string)$value);
    }
}
