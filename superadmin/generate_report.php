<?php
// Include the database connection file
require_once '../includes/db.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
require_once '../vendor/setasign/fpdf/fpdf.php';

// -------------------------- OCCUPANCY REPORT -------------------------- //
function get_occupancy_data() {
    global $conn;

    $query = "
        SELECT 
            r.room_number,
            r.type AS room_type,
            r.status AS room_status,
            u.full_name AS guest_name,
            u.email AS guest_email
        FROM rooms r
        LEFT JOIN (
            SELECT res1.*
            FROM reservations res1
            INNER JOIN (
                SELECT room_id, MAX(created_at) AS latest
                FROM reservations
                GROUP BY room_id
            ) latest_res ON res1.room_id = latest_res.room_id AND res1.created_at = latest_res.latest
        ) res ON r.id = res.room_id
        LEFT JOIN users u ON res.user_id = u.id
    ";

    $result = $conn->query($query);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    return $data;
}

function generate_report_occupancy($data) {
    $report = [];
    foreach ($data as $row) {
        $status = ucfirst($row['room_status']) ?? 'Vacant'; // 'occupied', 'reserved', etc.

        $report[] = [
            'Room Number' => $row['room_number'],
            'Room Type' => $row['room_type'],
            'Occupancy Status' => $status,
            'Guest Name' => $row['guest_name'] ?? 'N/A',
            'Guest Email' => $row['guest_email'] ?? 'N/A'
        ];
    }
    return $report;
}



// -------------------------- PERFORMANCE REPORT -------------------------- //
function get_performance_data($date_range) {
    global $conn;

    $query = "
        SELECT 
            s.id AS staff_id,
            s.name AS staff_name,
            a.activity_type,
            a.activity_date,
            a.activity_time,
            c.rating,
            w.start_time,
            w.end_time
        FROM 
            staff s
        LEFT JOIN activities a 
            ON a.user_id = s.user_id 
            AND a.activity_date BETWEEN ? AND ?
        LEFT JOIN customer_feedback c 
            ON c.customer_id = s.user_id
        LEFT JOIN working_hours w 
            ON w.staff_id = s.id
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $date_range['start'], $date_range['end']);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}




function calculate_performance_metrics($data) {
    $aggregated = [];

    foreach ($data as $row) {
        $id = $row['staff_id'];

        if (!isset($aggregated[$id])) {
            $aggregated[$id] = [
                'Staff ID' => $id,
                'Staff Name' => $row['staff_name'],
                'total_duration' => 0,
                'activity_count' => 0,
                'rating_sum' => 0,
                'rating_count' => 0,
                'attendance_blocks' => 0,
                'duration_logged' => false 
            ];
        }

        //  Only count working hours once per staff
        if (!$aggregated[$id]['duration_logged']) {
            $start = strtotime($row['start_time']);
            $end = strtotime($row['end_time']);
            $duration = ($start && $end) ? (($end - $start) / 3600) : 0;

            $aggregated[$id]['total_duration'] += $duration;
            $aggregated[$id]['attendance_blocks'] += $duration > 0 ? 1 : 0;
            $aggregated[$id]['duration_logged'] = true;
        }

        if (!empty($row['activity_type'])) {
            $aggregated[$id]['activity_count']++;
        }

        if (!empty($row['rating'])) {
            $aggregated[$id]['rating_sum'] += $row['rating'];
            $aggregated[$id]['rating_count']++;
        }


    }

    // Compute final metrics
    $metrics = [];
    foreach ($aggregated as $staff) {
        $productivity = ($staff['total_duration'] > 0 && $staff['activity_count'] > 0)
            ? ($staff['activity_count'] / $staff['total_duration']) * 100
            : 0;

        $quality = $staff['rating_count'] > 0
            ? ($staff['rating_sum'] / $staff['rating_count']) / 5 * 100
            : 0;

        $attendance_rate = $staff['attendance_blocks'] > 0 ? 100 : 0;

        $metrics[] = [
            'Staff ID' => $staff['Staff ID'],
            'Staff Name' => $staff['Staff Name'],
            'Productivity' => round($productivity, 2),
            'Quality' => round($quality, 2),
            'Attendance Rate' => round($attendance_rate, 2),
            'Overall Performance' => round(($productivity + $quality + $attendance_rate) / 3, 2)
        ];
    }

    return $metrics;
}

// -------------------------- EXPORT FUNCTIONS -------------------------- //
function export_to_pdf($report, $title, $filename) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);

    foreach ($report as $row) {
        foreach ($row as $key => $value) {
            $pdf->Cell(0, 10, "$key: $value", 0, 1);
        }
        $pdf->Ln(2);
    }

    $pdf->Output('F', $filename);
}

function export_to_excel($report, $sheet_title, $filename) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle($sheet_title);

    if (!empty($report)) {
        $headers = array_keys($report[0]);
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $row = 2;
        foreach ($report as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        $sheet->getStyle("A1:{$col}1")->getFont()->setBold(true);
    }

    $writer = new Xls($spreadsheet);
    $writer->save($filename);
}

// -------------------------- MAIN -------------------------- //
function main() {
    global $report_performance, $report_occupancy, $report_type;

    $report_type = $_POST['report_type'] ?? 'performance';
    $staff_search = trim($_POST['staff_search'] ?? '');
    $start_date = $_POST['start_date'] ?? date('Y-m-d');
    $end_date = $_POST['end_date'] ?? date('Y-m-d');

    $date_range = [
        'start' => $start_date,
        'end' => $end_date
    ];

    if ($report_type === 'performance') {
        $performance_data = get_performance_data($date_range);

        if ($staff_search !== '') {
            // Filter by staff name (case-insensitive)
            $performance_data = array_filter($performance_data, function($row) use ($staff_search) {
                return stripos($row['staff_name'], $staff_search) !== false;
            });
        }

        $report_performance = calculate_performance_metrics($performance_data);
        export_to_pdf($report_performance, 'Performance Report', 'staff_performance_report.pdf');
        export_to_excel($report_performance, 'Performance Report', 'staff_performance_report.xls');
    }

    if ($report_type === 'occupancy') {
        $occupancy_data = get_occupancy_data(); // you may remove date range if not needed
        $report_occupancy = generate_report_occupancy($occupancy_data);
        export_to_pdf($report_occupancy, 'Occupancy Report', 'daily_occupancy_report.pdf');
        export_to_excel($report_occupancy, 'Occupancy Report', 'daily_occupancy_report.xls');
    }
}

main();



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generated Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
       .form-section {
        max-width: 900px;
        margin: auto;
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
    }

    .form-section h2 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
    }

    .form-row {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .form-row label {
        font-weight: bold;
        margin-bottom: 6px;
        color: #444;
    }

    .form-row input[type="text"],
    .form-row input[type="date"],
    .form-row select {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 15px;
    }

    .form-actions {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-add {
            background: #28a745;
            color: white;
        }
        .btn-add:hover {
            background: #218838;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .btn-edit {
            background: #ffc107;
            color: black;
        }
        .btn-edit:hover {
            background: #e0a800;
        }
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<form method="POST" class="form-section">
    <h2>Generate Report</h2>

    <div class="form-row">
        <label for="report_type">Select Report</label>
        <select name="report_type" id="report_type" required>
            <option value="performance" <?= ($_POST['report_type'] ?? '') === 'performance' ? 'selected' : '' ?>>Staff Performance Report</option>
            <option value="occupancy" <?= ($_POST['report_type'] ?? '') === 'occupancy' ? 'selected' : '' ?>>Daily Occupancy Report</option>
        </select>
    </div>

    <div class="form-row">
        <label for="staff_search">Staff Name (optional)</label>
        <input type="text" name="staff_search" id="staff_search" placeholder="Search by staff name..." value="<?= $_POST['staff_search'] ?? '' ?>">
    </div>

    <div class="form-row">
        <label for="start_date">Start Date</label>
        <input type="date" name="start_date" value="<?= $_POST['start_date'] ?? '' ?>">
    </div>

    <div class="form-row">
        <label for="end_date">End Date</label>
        <input type="date" name="end_date" value="<?= $_POST['end_date'] ?? '' ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-add">🔍 Generate Report</button>
    </div>
</form>



<div class="container">
    <h2>Staff Performance Report</h2>
    <div class="table-container">
        <?php if (isset($_POST['report_type']) && $_POST['report_type'] === 'performance') : ?>

        <table>
            <tr>
                <th>Staff ID</th>
                <th>Staff Name</th>
                <th>Productivity</th>
                <th>Quality</th>
                <th>Attendance Rate</th>
                <th>Overall Performance</th>
            </tr>
            <?php if (isset($report_performance)) {
                foreach ($report_performance as $row) { ?>
                <tr>
                    <td><?php echo $row['Staff ID']; ?></td>
                    <td><?php echo $row['Staff Name']; ?></td>
                    <td><?php echo number_format($row['Productivity'], 2); ?>%</td>
                    <td><?php echo number_format($row['Quality'], 2); ?>%</td>
                    <td><?php echo number_format($row['Attendance Rate'], 2); ?>%</td>
                    <td><?php echo number_format($row['Overall Performance'], 2); ?>%</td>
                </tr>
            <?php } } ?>
        </table>
        <?php endif; ?>
    </div>

    <h2>Daily Occupancy Report</h2>
    <div class="table-container">
        <?php if (isset($_POST['report_type']) && $_POST['report_type'] === 'occupancy') : ?>

        <table>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Occupancy Status</th>
                <th>Guest Name</th>
                <th>Guest Email</th>
            </tr>
            <?php if (isset($report_occupancy)) {
                foreach ($report_occupancy as $row) { ?>
                <tr>
                    <td><?php echo $row['Room Number']; ?></td>
                    <td><?php echo $row['Room Type']; ?></td>
                    <td><?php echo $row['Occupancy Status']; ?></td>
                    <td><?php echo $row['Guest Name']; ?></td>
                    <td><?php echo $row['Guest Email']; ?></td>
                </tr>
            <?php } } ?>
        </table>
        <?php endif; ?>
    </div>

    <p>
        <a class="btn btn-add" href="staff_performance_report.pdf" target="_blank">Export Staff Report to PDF</a>
        <a class="btn btn-add" href="staff_performance_report.xls" target="_blank">Export Staff Report to Excel</a>
        <a class="btn btn-add" href="daily_occupancy_report.pdf" target="_blank">Export Occupancy Report to PDF</a>
        <a class="btn btn-add" href="daily_occupancy_report.xls" target="_blank">Export Occupancy Report to Excel</a>
    </p>
    <br>
     <a href="superadmin_dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>

</body>
</html>
