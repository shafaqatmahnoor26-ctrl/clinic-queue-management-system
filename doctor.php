<?php
include 'db.php';

$today = date('Y-m-d');

if (isset($_POST['next_patient'])) {
    $conn->query("UPDATE tokens SET status = 'completed' WHERE status = 'calling' AND created_at = '$today'");
    $get_waiting = $conn->query("SELECT id FROM tokens WHERE status = 'waiting' AND created_at = '$today' ORDER BY id ASC LIMIT 1");
    if ($get_waiting && $get_waiting->num_rows > 0) {
        $row = $get_waiting->fetch_assoc();
        $next_id = $row['id'];
        $conn->query("UPDATE tokens SET status = 'calling' WHERE id = '$next_id'");
    }
}

$active_res = $conn->query("SELECT token_number FROM tokens WHERE status = 'calling' AND created_at = '$today' ORDER BY id DESC LIMIT 1");
$current_active = ($active_res && $active_res->num_rows > 0) ? $active_res->fetch_assoc()['token_number'] : "None";

$waiting_res = $conn->query("SELECT COUNT(*) AS total FROM tokens WHERE status = 'waiting' AND created_at = '$today'");
$total_waiting = ($waiting_res) ? $waiting_res->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f172a; color: #ffffff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .glass-card { background: #1e293b; border: 2px solid #334155; border-radius: 24px; padding: 40px; width: 90vw; max-width: 550px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        .accent-text { color: #38bdf8; text-shadow: 0 0 20px rgba(56, 189, 248, 0.4); }
        .btn-call { background-color: #38bdf8; color: #0f172a; font-weight: 800; border: none; }
        .btn-call:hover { background-color: #0284c7; color: #ffffff; }
    </style>
</head>
<body>

<div class="glass-card text-center">
    <h3 class="fw-bold mb-4 text-light">Doctor Dashboard</h3>
    
    <div class="bg-dark p-4 rounded-4 mb-4 border border-secondary">
        <span class="text-uppercase text-muted d-block mb-1 small tracking-wider">Currently In Room</span>
        <div class="display-1 fw-bold accent-text my-1">
            <?php echo ($current_active !== "None") ? "#" . sprintf('%02d', $current_active) : "--"; ?>
        </div>
    </div>

    <!-- Fixed Line: Text colors changed to bright white and badge contrast fixed -->
    <p class="fs-6 text-light mb-4 fw-semibold">
        Patients in Waiting Room: 
        <span class="badge bg-info text-dark fs-6 ms-1 px-3 py-2 rounded-pill"><?php echo $total_waiting; ?></span>
    </p>

    <form method="POST">
        <button type="submit" name="next_patient" class="btn btn-call btn-lg w-100 py-3 rounded-3 fs-4 shadow">
            📢 Call Next Patient
        </button>
    </form>
</div>

</body>
</html>