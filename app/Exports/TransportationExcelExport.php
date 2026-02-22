<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use App\Models\Protsent;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransportationExcelExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $id, $start, $end, $costid;
    
    public function __construct($id, $start, $end, $costid)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->costid = $costid;
    }

    public function array(): array
    {
        $kindgar = Kindgarden::where('id', $this->id)->first();
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::all();
        $costs = Protsent::where('region_id', $kindgar->region_id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();

        $number_childrens = [];
        foreach($days as $day){
            foreach($ages as $age){
                $number_childrens[$day->id][$age->id] = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $this->id)
                    ->where('king_age_name_id', $age->id)
                    ->leftJoin('titlemenus', 'titlemenus.id', '=', 'number_childrens.kingar_menu_id')
                    ->first();
            }
        }

        $data = [];
        
        // Header qismi - PDF bilan bir xil
        $data[] = [$kindgar->kingar_name . ' да ' . $days[0]->day_number . '-' . $days[count($days)-1]->day_number . ' ' . $days[0]->month_name . ' ' . $days[0]->year_name . ' йил кунлари болалар катнови ва аутсорсинг хизмати харажатлари тўғрисида маълумот'];
        $data[] = [''];
        
        // Jadval header - PDF bilan bir xil struktura
        $header1 = ['№', 'Таомнома', 'Сана', 'Буюртма бўйича бола сони', '', '', 'Бир нафар болага сарфланган харажат НДС билан', '', 'Жами етказиб бериш харажат НДС билан', '', ''];
        
        // Age range headers qo'shish - har bir yosh guruhi uchun 4 ustun
        foreach($ages as $age) {
            $header1[] = 'Жами етказиб бериш харажатлари (' . $age->description . ')';
            $header1[] = '';
            $header1[] = '';
            $header1[] = '';
        }
        $header1[] = 'Жами етказиб бериш суммаси (НДС билан)';
        
        $data[] = $header1;
        
        // Sub header
        $raise = $costs->where('age_range_id', 4)->first()->raise ?? 28.5;
        $nds = $costs->where('age_range_id', 4)->first()->nds ?? 12;
        
        $header2 = ['', '', '', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', 'Жами', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', 'Жами'];
        
        foreach($ages as $age) {
            $header2[] = 'Сумма (безНДС)';
            $header2[] = 'Устама ҳақ ' . $raise . '%';
            $header2[] = 'ҚҚС (НДС) ' . $nds . '%';
            $header2[] = 'Жами сумма';
        }
        $header2[] = '';
        
        $data[] = $header2;
        
        // Ma'lumotlar qatorlari
        $row_number = 1;
        $currentDataRow = 5;
        
        // Jami hisoblash uchun o'zgaruvchilar
        $total_children_9_10 = 0;
        $total_children_4 = 0;
        $total_children_all = 0;
        $total_cost_9_10 = 0;
        $total_cost_4 = 0;
        $total_delivery_9_10 = 0;
        $total_delivery_4 = 0;
        $total_delivery_all = 0;
        $total_amount_without_nds9_10 = 0;
        $total_amount_without_nds4 = 0;
        $total_markup9_10 = 0;
        $total_markup4 = 0;
        $total_nds9_10 = 0;
        $total_nds4 = 0;
        $total_final_amount9_10 = 0;
        $total_final_amount4 = 0;
        $total_final_amount = 0;
        
        foreach($days as $day) {
            // Bolalar sonini hisoblash
            $children_9_10 = 0;
            $children_4 = 0;
            $menu_name = '';
            
            foreach($number_childrens[$day->id] as $age_id => $child) {
                if($age_id == 4) { // 9-10.5 soatlik guruh
                    $menu_name = $child->menu_name ?? '';
                    $children_9_10 += $child->kingar_children_number ?? 0;
                } elseif($age_id == 3) { // 4 soatlik guruh
                    $children_4 += $child->kingar_children_number ?? 0;
                }
            }
            
            $eater_cost9_10 = $costs->where('age_range_id', 4)->first()->eater_cost ?? 0;
            $eater_cost4 = $costs->where('age_range_id', 3)->first()->eater_cost ?? 0;
            
            // Hisoblashlar
            $children_all = $children_9_10 + $children_4;
            $delivery_9_10 = $children_9_10 * $eater_cost9_10;
            $delivery_4 = $children_4 * $eater_cost4;
            $delivery_all = $delivery_9_10 + $delivery_4;
            
            // Age range calculations - har bir yosh guruhi uchun alohida
            $amount_without_nds9_10 = $delivery_9_10 / (1 + ($nds / 100));
            $amount_without_nds4 = $delivery_4 / (1 + ($nds / 100));
            $markup9_10 = $amount_without_nds9_10 * ($raise / 100);
            $markup4 = $amount_without_nds4 * ($raise / 100);
            $nds9_10 = $amount_without_nds9_10 * ($nds / 100);
            $nds4 = $amount_without_nds4 * ($nds / 100);
            $final_amount9_10 = $amount_without_nds9_10 + $markup9_10 + $nds9_10;
            $final_amount4 = $amount_without_nds4 + $markup4 + $nds4;
            $final_amount = $final_amount9_10 + $final_amount4;
            
            // Jami hisoblash
            $total_children_9_10 += $children_9_10;
            $total_children_4 += $children_4;
            $total_children_all += $children_all;
            $total_cost_9_10 += $eater_cost9_10;
            $total_cost_4 += $eater_cost4;
            $total_delivery_9_10 += $delivery_9_10;
            $total_delivery_4 += $delivery_4;
            $total_delivery_all += $delivery_all;
            $total_amount_without_nds9_10 += $amount_without_nds9_10;
            $total_amount_without_nds4 += $amount_without_nds4;
            $total_markup9_10 += $markup9_10;
            $total_markup4 += $markup4;
            $total_nds9_10 += $nds9_10;
            $total_nds4 += $nds4;
            $total_final_amount9_10 += $final_amount9_10;
            $total_final_amount4 += $final_amount4;
            $total_final_amount += $final_amount;
            
            $row = [
                $row_number++,
                $menu_name,
                $day->day_number . '/' . $day->month_name . '/' . $day->year_name,
                $children_9_10,
                $children_4,
                $children_all,
                $eater_cost9_10,
                $eater_cost4,
                $delivery_9_10,
                $delivery_4,
                $delivery_all
            ];
            
            // Age range data qo'shish - har bir yosh guruhi uchun 4 ustun
            foreach($ages as $age) {
                if($age->id == 4) { // 9-10.5 soatlik
                    $row[] = $amount_without_nds9_10;
                    $row[] = $markup9_10;
                    $row[] = $nds9_10;
                    $row[] = $final_amount9_10;
                } elseif($age->id == 3) { // 4 soatlik
                    $row[] = $amount_without_nds4;
                    $row[] = $markup4;
                    $row[] = $nds4;
                    $row[] = $final_amount4;
                } else {
                    // Boshqa yosh guruhlari uchun 0
                    $row[] = 0;
                    $row[] = 0;
                    $row[] = 0;
                    $row[] = 0;
                }
            }
            
            // Final total
            $row[] = $final_amount;
            
            $data[] = $row;
            $currentDataRow++;
        }
        
        // Jami qatori - PDF bilan bir xil
        $totalRow = [
            '',
            '',
            'ЖАМИ',
            $total_children_9_10,
            $total_children_4,
            $total_children_all,
            $total_cost_9_10,
            $total_cost_4,
            $total_delivery_9_10,
            $total_delivery_4,
            $total_delivery_all
        ];
        
        // Age range totals qo'shish
        foreach($ages as $age) {
            if($age->id == 4) { // 9-10.5 soatlik
                $totalRow[] = $total_amount_without_nds9_10;
                $totalRow[] = $total_markup9_10;
                $totalRow[] = $total_nds9_10;
                $totalRow[] = $total_final_amount9_10;
            } elseif($age->id == 3) { // 4 soatlik
                $totalRow[] = $total_amount_without_nds4;
                $totalRow[] = $total_markup4;
                $totalRow[] = $total_nds4;
                $totalRow[] = $total_final_amount4;
            } else {
                // Boshqa yosh guruhlari uchun 0
                $totalRow[] = 0;
                $totalRow[] = 0;
                $totalRow[] = 0;
                $totalRow[] = 0;
            }
        }
        
        // Final total
        $totalRow[] = $total_final_amount;
        $data[] = $totalRow;
        
        // Imzo qismi
        $data[] = [''];
        $data[] = ['Аутсорсер директори: ____________________________'];
        $data[] = ['Буюртмачи директори: ____________________________'];
        
        return $data;
    }
    
    private function getColumnLetter($columnNumber) {
        $dividend = $columnNumber;
        $columnName = '';
        while ($dividend > 0) {
            $modulo = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend = intval(($dividend - $modulo) / 26);
        }
        return $columnName;
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Header merge va style
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->getStyle('A1')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                
                // Jadval header merge cells - PDF bilan bir xil
                $sheet->mergeCells('A3:A4'); // №
                $sheet->mergeCells('B3:B4'); // Таомнома
                $sheet->mergeCells('C3:C4'); // Сана
                $sheet->mergeCells('D3:F3'); // Буюртма бўйича бола сони
                $sheet->mergeCells('G3:H3'); // Бир нафар болага сарфланган харажат НДС билан
                $sheet->mergeCells('I3:K3'); // Жами етказиб бериш харажат НДС билан
                
                // Age range headers - har bir yosh guruhi uchun 4 ustun
                $col = 'L';
                foreach(Age_range::all() as $age) {
                    $endCol = chr(ord($col) + 3);
                    $sheet->mergeCells($col . '3:' . $endCol . '3');
                    $col = chr(ord($endCol) + 1);
                }
                $sheet->mergeCells($col . '3:' . $col . '4'); // Final total
                
                // Header style
                $sheet->getStyle('A3:' . $highestColumn . '4')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0'],
                    ],
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Ma'lumotlar border
                $dataStartRow = 5;
                $dataEndRow = $highestRow - 3; // Imzo qatorlaridan oldin
                $dataRange = 'A' . $dataStartRow . ':' . $highestColumn . $dataEndRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                
                // Jami qatori style
                $totalRow = $dataEndRow;
                $sheet->getStyle('A' . $totalRow . ':' . $highestColumn . $totalRow)
                      ->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow . ':' . $highestColumn . $totalRow)
                      ->applyFromArray([
                          'fill' => [
                              'fillType' => Fill::FILL_SOLID,
                              'startColor' => ['rgb' => 'D0D0D0'],
                          ],
                          'borders' => [
                              'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                          ],
                      ]);
                
                // Number format - raqamli ustunlar uchun
                $sheet->getStyle('D' . $dataStartRow . ':' . $highestColumn . $dataEndRow)
                      ->getNumberFormat()->setFormatCode('#,##0.00');
                
                // Imzo qatorlari style
                $signatureRow1 = $highestRow - 2;
                $signatureRow2 = $highestRow - 1;
                $sheet->getStyle('A' . $signatureRow1 . ':A' . $signatureRow1)
                      ->getFont()->setBold(true);
                $sheet->getStyle('A' . $signatureRow2 . ':A' . $signatureRow2)
                      ->getFont()->setBold(true);
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // №
            'B' => 12,  // Таомнома
            'C' => 15,  // Сана
            'D' => 12,  // 9-10.5 соатлик бола сони
            'E' => 12,  // 4 соатлик бола сони
            'F' => 10,  // Жами
            'G' => 12,  // 9-10.5 соатлик нарх
            'H' => 12,  // 4 соатлик нарх
            'I' => 15,  // 9-10.5 delivery
            'J' => 15,  // 4 delivery
            'K' => 15,  // Жами delivery
        ];
    }
} 