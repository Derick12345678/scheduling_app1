<?php include('header1.php'); ?>

<body>
<div id="navbar">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a href="index.php" class="navbar-brand mr-4">Home</a> 
        <a href="calendar.php" class="navbar-brand mr-4">Calendar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>
</div>

<div id="controls">
        <button class="btn btn-info" id="prev-month">Previous</button>
        <span class="" id="current-month-year"></span>
        <button class="btn btn-info" id="next-month">Next</button>
    </div>
    <div id="calendar-container">
        <div class="" id="app-calendar"></div>
        <div id="day-info">Click a date to see details.</div>
    </div>
<div id="app-calendar"></div>
<script src="calendar.js"></script>
</body>