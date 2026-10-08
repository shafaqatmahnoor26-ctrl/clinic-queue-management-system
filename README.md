# Clinic Queue Management System

A real-time patient queue system with three screens: Reception, Doctor and Waiting-room Display.

## How it works
- **Reception (reception.php):** Issues a numbered token with a printed slip and adds the patient to the active queue.
- **Doctor (doctor.php):** Shows the live waiting count. "Call Next Patient" updates the queue instantly.
- **Display (display.php):** Auto-updates using JavaScript Fetch polling and plays an audio alert (Web Audio API) when a patient is called.

## Technologies
PHP, MySQL, JavaScript, Bootstrap, HTML, CSS
