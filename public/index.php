<?php
    if (@$_GET['schedule'] === '1') {
        require __DIR__ . '/gemini-schedule.php';
        exit;
    }
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Set Schedule Parameters</h2>
    <form action="/" method="get">
        <input type="hidden" name="schedule" value="1">
        <div class="form-group">
            <label for="workers_count">Workers Count:</label>
            <input type="number" id="workers_count" name="workers_count" min="1" max="50" value="3" required>
        </div>
        <div class="form-group">
            <label for="week_hours">Week Hours:</label>
            <input type="number" id="week_hours" name="week_hours" min="1" max="1000" value="40" required>
        </div>
        <div class="form-group">
            <label for="from">Start Hour (From):</label>
            <input type="number" id="from" name="from" min="0" max="24" value="8" required>
        </div>
        <div class="form-group">
            <label for="to">End Hour (To):</label>
            <input type="number" id="to" name="to" min="0" max="24" value="23" required>
        </div>
        <div class="form-group">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>

</body>
</html>