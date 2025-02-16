<?php include('header1.php'); ?>
<style>
    .month-year-slot{
    font-weight: normal !important; 
    border: 1px solid black; 
    padding: 2px 5px; 
    margin-left: 0px !important;
    border-radius: 5px;
    color: #f9f9f9;
    background-color: #1E2022;
    }

    #prev-month{
        margin-right: 0px !important;
    }
</style>
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
        <span class="month-year-slot" id="current-month-year"></span>
        <button class="btn btn-info" id="next-month">Next</button>
    </div>
    <div id="calendar-container">
        <div class="" id="app-calendar"></div>
        <div id="day-info">Click a date to see details.</div>
    </div>
<div id="app-calendar"></div>
<script src="calendar.js"></script>
</body>