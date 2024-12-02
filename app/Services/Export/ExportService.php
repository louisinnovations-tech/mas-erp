<?php

namespace App\Services\Export;

use App\Models\Meeting;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
    public function exportMeetings(array $filters = [])
    {
        $meetings = Meeting::query()
            ->with(['participants', 'documents'])
            ->when(!empty($filters['date_range']), function($query) use ($filters) {
                return $query->whereBetween('start_time', [
                    $filters['date_range']['start'],
                    $filters['date_range']['end']
                ]);
            })
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Title');
        $sheet->setCellValue('B1', 'Start Time');
        $sheet->setCellValue('C1', 'Duration');
        $sheet->setCellValue('D1', 'Participants');
        $sheet->setCellValue('E1', 'Status');

        // Data
        $row = 2;
        foreach ($meetings as $meeting) {
            $sheet->setCellValue('A' . $row, $meeting->title);
            $sheet->setCellValue('B' . $row, $meeting->start_time->format('Y-m-d H:i'));
            $sheet->setCellValue('C' . $row, $meeting->duration . ' minutes');
            $sheet->setCellValue('D' . $row, $meeting->participants->pluck('name')->implode(', '));
            $sheet->setCellValue('E' . $row, $meeting->status);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'meetings_export_' . now()->format('Y-m-d_His') . '.xlsx';
        $path = 'exports/' . $filename;
        
        Storage::put($path, '');
        $writer->save(Storage::path($path));

        return $path;
    }

    public function exportMeetingDetails(Meeting $meeting)
    {
        $spreadsheet = new Spreadsheet();
        
        // Meeting Details Sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Meeting Details');
        
        $sheet->setCellValue('A1', 'Meeting Title');
        $sheet->setCellValue('B1', $meeting->title);
        
        $sheet->setCellValue('A2', 'Date & Time');
        $sheet->setCellValue('B2', $meeting->start_time->format('Y-m-d H:i'));
        
        $sheet->setCellValue('A3', 'Duration');
        $sheet->setCellValue('B3', $meeting->duration . ' minutes');
        
        // Participants Sheet
        $participantSheet = $spreadsheet->createSheet();
        $participantSheet->setTitle('Participants');
        
        $participantSheet->setCellValue('A1', 'Name');
        $participantSheet->setCellValue('B1', 'Email');
        $participantSheet->setCellValue('C1', 'Role');
        
        $row = 2;
        foreach ($meeting->participants as $participant) {
            $participantSheet->setCellValue('A' . $row, $participant->name);
            $participantSheet->setCellValue('B' . $row, $participant->email);
            $participantSheet->setCellValue('C' . $row, $participant->pivot->role ?? 'Participant');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'meeting_details_' . $meeting->id . '_' . now()->format('Y-m-d_His') . '.xlsx';
        $path = 'exports/' . $filename;
        
        Storage::put($path, '');
        $writer->save(Storage::path($path));

        return $path;
    }
}