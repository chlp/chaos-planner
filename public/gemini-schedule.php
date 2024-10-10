<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/../gcloud-service-key.json');
function getAccessToken() {
    // Путь к файлу учетных данных сервисного аккаунта
    $credentialsPath = getenv('GOOGLE_APPLICATION_CREDENTIALS');

    if (!$credentialsPath || !file_exists($credentialsPath)) {
        throw new Exception('The GOOGLE_APPLICATION_CREDENTIALS environment variable is not set or the file does not exist.');
    }

    $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
    $credentials = new ServiceAccountCredentials($scopes, $credentialsPath);

    $accessToken = $credentials->fetchAuthToken();

    if (isset($accessToken['access_token'])) {
        return $accessToken['access_token'];
    } else {
        throw new Exception('Failed to generate access token. Please check the credentials.');
    }
}

try {
    $token = getAccessToken();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

$curl = curl_init();

$workersCount = (int)(@$_GET['workers_count'] ?: 1);
$from = (int)(@$_GET['from'] ?: 8);
$to = (int)(@$_GET['to'] ?: 18);
$weekHours = (int)(@$_GET['week_hours'] ?: 40);

$projectId = 'sunny-buttress-438111-g7';
$modelName = 'gemini-1.5-flash';
$location = 'europe-west1';
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://us-central1-aiplatform.googleapis.com/v1/projects/' . $projectId . '/locations/' . $location . '/publishers/google/models/' . $modelName . ':streamGenerateContent',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 100,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
    "contents":
    {
        "role": "user",
        "parts":
        [
            {
                "text": "Generate real name for each person"
            },
            {
                "text": "Schedule work hours for ' . $workersCount . ' persons."
            },
            {
                "text": "Each person could work day from ' . $from . ':00 to ' . $to . ':00"
            },
            {
                "text": "Total ' . $weekHours . ' hours per week for each person. It is maximum to work"
            },
            {
                "text": "hours = to - from"
            },
            {
                "text": "left_hours_on_day_start = left_hours_on_day_end (on previous day) or total hours for first day"
            },
            {
                "text": "left_hours_on_day_end = left_hours_on_day_start - hours"
            },
            {
                "text": "Each person should try to work on different days and hours with other persons"
            },
            {
                "text": "Each person should try to start and end work on different hours each day"
            },
            {
                "text": "Work days should start from Monday and end on Sunday"
            },
            {
                "text": "Stop generating on left_hours_on_day_end==0. left_hours_on_day_end should be > 0"
            }
        ]
    },
    "systemInstruction":
    {
        "role": "user",
        "parts":
        [
            {
                "text": "check that \"hours\" equals \"to\" minus \"from\" hours"
            },
            {
                "text": "\"left_hours_on_day_start\" equals \"left_hours_on_day_end\" on previous day or total hours on first day"
            },
            {
                "text": "\"left_hours_on_day_end\" equals \"left_hours_on_day_start\" minus \"hours\""
            }
        ]
    },
    "generationConfig":
    {
        "temperature": 0,
        "candidateCount": 1,
        "responseMimeType": "application/json",
        "responseSchema":
        {
            "type": "array",
            "items":
            {
                "type": "object",
                "properties":
                {
                    "name":
                    {
                        "type": "string"
                    },
                    "schedule":
                    {
                        "type": "array",
                        "items":
                        {
                            "type": "object",
                            "properties":
                            {
                                "week_day":
                                {
                                    "type": "string",
                                    "enum":
                                    [
                                        "Monday",
                                        "Tuesday",
                                        "Wednesday",
                                        "Thursday",
                                        "Friday",
                                        "Saturday",
                                        "Sunday"
                                    ]
                                },
                                "from":
                                {
                                    "type": "string"
                                },
                                "to":
                                {
                                    "type": "string"
                                },
                                "hours":
                                {
                                    "type": "integer"
                                },
                                "left_hours_on_day_start":
                                {
                                    "type": "integer"
                                },
                                "left_hours_on_day_end":
                                {
                                    "type": "integer"
                                }
                            },
                            "required":
                            [
                                "week_day",
                                "from",
                                "to",
                                "left_hours_on_day_start",
                                "left_hours_on_day_end"
                            ]
                        }
                    }
                },
                "required":
                [
                    "name",
                    "schedule"
                ]
            }
        }
    }
}',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);

$data = json_decode($response, true);

$combinedText = '';
foreach ($data as $entry) {
    if (isset($entry['candidates'])) {
        foreach ($entry['candidates'] as $candidate) {
            if (isset($candidate['content']['parts'])) {
                foreach ($candidate['content']['parts'] as $part) {
                    if (isset($part['text'])) {
                        $combinedText .= $part['text'];
                    }
                }
            }
        }
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }

        .schedule-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .employee-name {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
        }
    </style>
</head>
<body>

<div id="schedules"></div>

<script>
    const data = <?=$combinedText?>;

    const schedulesContainer = document.getElementById('schedules');

    data.forEach(employee => {
        const container = document.createElement('div');
        container.className = 'schedule-container';

        const nameHeader = document.createElement('div');
        nameHeader.className = 'employee-name';
        nameHeader.textContent = employee.name;
        container.appendChild(nameHeader);

        const table = document.createElement('table');
        table.innerHTML = `
            <thead>
                <tr>
                    <th>Week Day</th>
                    <th>From</th>
                    <th>To</th>
                </tr>
            </thead>
            <tbody>
                ${employee.schedule.map(entry => `
                    <tr>
                        <td>${entry.week_day}</td>
                        <td>${entry.from}</td>
                        <td>${entry.to}</td>
                    </tr>
                `).join('')}
            </tbody>
        `;

        container.appendChild(table);
        schedulesContainer.appendChild(container);
    });
</script>

</body>
</html>