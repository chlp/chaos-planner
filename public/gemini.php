<?php

$projectId = 'sunny-buttress-438111-g7';
$modelName = 'gemini-1.5-flash';
$location = 'europe-west1';
$token = file_get_contents(__DIR__ . '/../gemini-token.txt');

$curl = curl_init();

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
                "text": "Schedule work hours for 3 persons."
            },
            {
                "text": "1st person could work each day from 00:00 to 12:00. Maximum 4 hours per day. Total 20 hours per week"
            },
            {
                "text": "2nd person could work week days from 07:00 to 24:00. Maximum 10 hours per day. Total 50 hours per week"
            },
            {
                "text": "3rd person could work each day from 00:00 to 24:00. Maximum 16 hours per day. Total 100 hours per week"
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

var_dump(json_decode($combinedText, true));

echo "\n\n<br><br>\n\n";

echo $combinedText;

echo "\n\n<br><br>\n\n";

echo $response;
