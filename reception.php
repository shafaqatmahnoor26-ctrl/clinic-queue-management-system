<?php
include 'db.php';

$today = date('Y-m-d');
$printed_token = null;

if (isset($_POST['generate_token'])) {
    $sql = "SELECT MAX(token_number) AS max_token FROM tokens WHERE created_at = '$today'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $next_token = ($row['max_token'] !== null) ? $row['max_token'] + 1 : 1;

    $insert = "INSERT INTO tokens (token_number, status, created_at) VALUES ('$next_token', 'waiting', '$today')";
    if ($conn->query($insert)) {
        $printed_token = $next_token;
    }
}

$active_res = $conn->query("SELECT token_number FROM tokens WHERE status = 'calling' AND created_at = '$today' ORDER BY id DESC LIMIT 1");
$current_calling = ($active_res && $active_res->num_rows > 0) ? $active_res->fetch_assoc()['token_number'] : "None";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reception - Token Counter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f172a; color: #ffffff; min-height: 100vh; display: flex; align-items: center; }
        .glass-card { background: #1e293b; border: 2px solid #334155; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        .accent-text { color: #38bdf8; text-shadow: 0 0 15px rgba(56, 189, 248, 0.3); }
        .btn-cyan { background-color: #38bdf8; color: #0f172a; font-weight: 700; border: none; }
        .btn-cyan:hover { background-color: #0284c7; color: #ffffff; }

        /* Thermal Printer Optimized CSS */
        @media print {
            body * { visibility: hidden; }
            #printableSlip, #printableSlip * { visibility: visible; }
            #printableSlip { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 80mm; 
                padding: 15px; 
                background: #ffffff !important; 
                color: #000000 !important;
                border: none !important;
            }
            @page { size: auto; margin: 0mm; }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row g-4 align-items-stretch">
        
        <!-- LEFT 50%: Active Token Display -->
        <div class="col-md-6 d-flex">
            <div class="glass-card p-5 w-100 text-center d-flex flex-column justify-content-center">
                <span class="text-uppercase tracking-wider text-muted fw-bold mb-2">NOW INSIDE ROOM</span>
                <div class="display-1 fw-bold accent-text my-3" id="activeToken">
                    <?php echo ($current_calling !== "None") ? "#" . sprintf('%02d', $current_calling) : "--"; ?>
                </div>
                <p class="text-secondary mb-0">Ultrasound Examination Room</p>
            </div>
        </div>

        <!-- RIGHT 50%: Token Issuing & Print Slip -->
        <div class="col-md-6 d-flex">
            <div class="glass-card p-4 w-100 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="fw-bold text-light mb-1">Ultrasound Clinic</h3>
                    <p class="text-muted small mb-4">Click below to generate and issue a new patient token.</p>

                    <form method="POST">
                        <button type="submit" name="generate_token" class="btn btn-cyan btn-lg w-100 py-3 rounded-3 fs-5 shadow">
                            🎟️ Issue New Token
                        </button>
                    </form>
                </div>

                <?php if ($printed_token): ?>
                    <div id="printableSlip" class="p-3 border border-secondary rounded-3 bg-light text-dark text-center mt-3">
                        <h4 class="fw-bold mb-1" style="color:#000;">Ultrasound Clinic</h4>
                        <p class="small text-muted mb-1" style="font-size: 0.85rem;">Date: <?php echo date('d-M-Y h:i A'); ?></p>
                        <hr style="border-top: 1px dashed #000; margin: 8px 0;">
                        <span style="font-size: 0.9rem; font-weight: bold; text-transform: uppercase;">Your Token Number</span>
                        <div class="display-3 fw-bold my-1" style="color:#000;">#<?php echo sprintf('%02d', $printed_token); ?></div>
                        <p class="small text-muted mb-0" style="font-size: 0.8rem;">Please wait for your turn.</p>
                    </div>
                    <button onclick="window.print()" class="btn btn-outline-light w-100 mt-2 fw-bold">🖨️ Print Slip</button>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
setInterval(() => {
    fetch('get_active.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('activeToken').innerText = data.token ? "#" + String(data.token).padStart(2, '0') : "--";
        });
}, 2000);
</script>

</body>
</html>