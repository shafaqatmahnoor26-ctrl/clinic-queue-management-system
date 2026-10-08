<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Outer Display Screen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f172a; color: #ffffff; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .token-box { background: #1e293b; border: 4px solid #38bdf8; border-radius: 28px; padding: 50px; width: 85vw; max-width: 950px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7); }
        .token-number { font-size: 15rem; font-weight: 900; color: #38bdf8; line-height: 1; text-shadow: 0 0 40px rgba(56, 189, 248, 0.5); }
    </style>
</head>
<body>

<div class="token-box text-center">
    <h1 class="display-3 text-uppercase fw-bold tracking-wide text-light mb-2">NOW CALLING</h1>
    <div class="token-number my-2" id="currentToken">--</div>
    <h2 class="text-secondary mt-3 fw-semibold">Ultrasound Examination Room</h2>
</div>

<script>
let lastToken = null;
let audioCtx = null;

// Function to generate and play chime/bell sound
function playBell() {
    try {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        
        osc.type = 'sine';
        osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5 pitch note
        gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 1.2);
        
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 1.2);
    } catch(e) {
        console.log("Audio play error:", e);
    }
}

// Enable audio context on first click anywhere on screen
document.body.addEventListener('click', () => {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
}, { once: true });

function fetchActiveToken() {
    fetch('get_active.php')
        .then(response => response.json())
        .then(data => {
            const tokenElement = document.getElementById('currentToken');
            if (data.token) {
                const newToken = "#" + String(data.token).padStart(2, '0');
                
                // Play sound only when a new token is called
                if (lastToken !== null && lastToken !== newToken) {
                    playBell();
                }
                
                lastToken = newToken;
                tokenElement.innerText = newToken;
            } else {
                tokenElement.innerText = "--";
            }
        });
}

setInterval(fetchActiveToken, 2000);
fetchActiveToken();
</script>

</body>
</html>